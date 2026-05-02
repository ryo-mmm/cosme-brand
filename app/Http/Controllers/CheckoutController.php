<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionStartedMail;
use App\Models\Product;
use App\Services\AlertService;
use App\Services\StripeErrorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Exceptions\IncompletePayment;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $productIds = $request->input('products', []);
        $products = Product::with('skinTypes')->whereIn('id', $productIds)->active()->get();
        $type = $request->input('type', 'subscription');

        $intent = null;
        $savedAddress = null;
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $intent = $user->createSetupIntent();
            $savedAddress = $user->only(['postal_code', 'address', 'address_line2']);
        }

        return view('checkout', compact('products', 'intent', 'type', 'savedAddress'));
    }

    public function process(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->hasVerifiedEmail()) {
            return back()->with('error', '決済を行うにはメールアドレスの確認が必要です。確認メールをご確認ください。');
        }

        $request->validate([
            'payment_method' => ['required', 'string'],
            'products'       => ['required', 'array', 'min:1', 'max:10'],
            'products.*'     => ['required', 'integer', 'exists:products,id'],
            'type'           => ['required', 'in:subscription,single'],
            'postal_code'    => ['required', 'string', 'max:20'],
            'address'        => ['required', 'string', 'max:255'],
            'address_line2'  => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($request->payment_method);

            $user->update([
                'postal_code'   => $request->postal_code,
                'address'       => $request->address,
                'address_line2' => $request->address_line2,
            ]);

            if ($request->type === 'subscription') {
                return $this->processSubscription($user, $request);
            }

            return $this->processSinglePurchase($user, $request);

        } catch (IncompletePayment $e) {
            return redirect()->route('cashier.payment', [
                $e->payment->id,
                'redirect' => route('mypage'),
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            StripeErrorService::log($e, [
                'user_id'       => $user->id,
                'purchase_type' => $request->type,
            ]);
            return back()->with('error', StripeErrorService::toJapanese($e));
        } catch (\Stripe\Exception\ApiErrorException $e) {
            StripeErrorService::log($e, [
                'user_id'       => $user->id,
                'purchase_type' => $request->type,
                'action'        => 'checkout',
            ]);
            return back()->with('error', 'Stripe処理中にエラーが発生しました。しばらくしてから再度お試しください。');
        } catch (\Exception $e) {
            AlertService::critical('checkout.unexpected_error', [
                'user_id'        => $user->id,
                'purchase_type'  => $request->type,
                'exception_class' => get_class($e),
                'exception_message' => mb_substr($e->getMessage(), 0, 200),
            ]);
            return back()->with('error', '決済処理中にエラーが発生しました。カード情報をご確認のうえ、もう一度お試しください。');
        }
    }

    public function thanks(Request $request)
    {
        $type = $request->query('type', 'subscription');
        return view('checkout-thanks', compact('type'));
    }

    private function processSubscription(\App\Models\User $user, Request $request)
    {
        $priceId = config('cashier.price_id');

        if (!$priceId) {
            return back()->with('error', '定期便の価格設定が未完了です。管理者にお問い合わせください。');
        }

        if ($user->subscribed('default')) {
            return redirect()->route('mypage')->with('error', 'すでに定期便にご登録済みです。');
        }

        $user->newSubscription('default', $priceId)
            ->create($request->payment_method);

        Log::info('checkout.subscription_started', [
            'user_id'  => $user->id,
            'price_id' => $priceId,
        ]);

        Mail::to($user)->queue(new SubscriptionStartedMail($user));

        return redirect()->route('checkout.thanks', ['type' => 'subscription']);
    }

    private function processSinglePurchase(\App\Models\User $user, Request $request)
    {
        // 同一商品が複数含まれる場合に数量として扱う (例: [1,1,2] → {1:2, 2:1})
        $quantityMap = collect($request->products)->countBy()->all();
        $uniqueIds   = array_keys($quantityMap);

        // Phase 1: 在庫確認 & 引き当て（短命トランザクション＋悲観的ロック）
        $stockError = $this->reserveStock($uniqueIds, $quantityMap);
        if ($stockError !== null) {
            return back()->with('error', $stockError);
        }

        // Phase 2: Stripe 決済（トランザクション外でロック解放済み）
        $products = Product::whereIn('id', $uniqueIds)->get()->keyBy('id');

        $subtotal = 0;
        foreach ($quantityMap as $id => $qty) {
            $subtotal += (float) $products[$id]->price * $qty;
        }
        $total = (int) (($subtotal + config('subscription.shipping_fee')) * 100);

        try {
            $user->charge($total, $request->payment_method, [
                'description' => '単品購入: ' . $products->pluck('name')->join(', '),
                'metadata'    => [
                    'product_ids' => $products->pluck('id')->join(','),
                    'user_id'     => $user->id,
                ],
            ]);
        } catch (\Exception $e) {
            // 決済失敗時に引き当て済み在庫を戻す
            foreach ($quantityMap as $id => $qty) {
                Product::where('id', $id)->increment('stock', $qty);
            }
            Log::warning('checkout.stock_restored_on_payment_failure', [
                'user_id'      => $user->id,
                'product_ids'  => $uniqueIds,
                'quantity_map' => $quantityMap,
                'amount_yen'   => (int) ($total / 100),
            ]);
            throw $e;
        }

        Log::info('checkout.single_purchase_completed', [
            'user_id'      => $user->id,
            'product_ids'  => $uniqueIds,
            'amount_yen'   => (int) ($total / 100),
        ]);

        return redirect()->route('checkout.thanks', ['type' => 'single']);
    }

    /**
     * 在庫を悲観的ロックで確認・引き当てる。
     * トランザクションを即コミットしてロック保持時間を最小化する。
     *
     * @return string|null エラーメッセージ（nullなら成功）
     */
    private function reserveStock(array $uniqueIds, array $quantityMap): ?string
    {
        $error = null;

        DB::transaction(function () use ($uniqueIds, $quantityMap, &$error) {
            // SELECT ... FOR UPDATE で行ロック取得
            $products = Product::lockForUpdate()
                ->whereIn('id', $uniqueIds)
                ->active()
                ->get()
                ->keyBy('id');

            // 商品の存在確認（アクティブ外・削除済み含む）
            if ($products->count() !== count($uniqueIds)) {
                $error = '一部の商品が見つかりませんでした。';
                return; // ロック取得後の空コミットで正常終了
            }

            // 在庫数チェック
            foreach ($quantityMap as $id => $qty) {
                $product = $products[$id];
                if ($product->stock < $qty) {
                    $error = $product->stock === 0
                        ? "「{$product->name}」は在庫切れのため購入できません。"
                        : "「{$product->name}」の在庫が不足しています（残り {$product->stock} 個）。";
                    return;
                }
            }

            // 在庫引き当て（減算）
            foreach ($quantityMap as $id => $qty) {
                Product::where('id', $id)->decrement('stock', $qty);
            }
        });

        return $error;
    }
}
