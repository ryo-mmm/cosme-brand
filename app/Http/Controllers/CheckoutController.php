<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Exceptions\IncompletePayment;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $productIds = $request->input('products', []);
        $products = Product::whereIn('id', $productIds)->active()->get();
        $type = $request->input('type', 'subscription');

        $intent = null;
        if (Auth::check()) {
            $intent = Auth::user()->createSetupIntent();
        }

        return view('checkout', compact('products', 'intent', 'type'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', 'string'],
            'products'       => ['required', 'array'],
            'type'           => ['required', 'in:subscription,single'],
            'postal_code'    => ['required', 'string', 'max:20'],
            'address'        => ['required', 'string', 'max:255'],
            'address_line2'  => ['nullable', 'string', 'max:255'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($request->payment_method);

            if ($request->type === 'subscription') {
                return $this->processSubscription($user, $request);
            }

            return $this->processSinglePurchase($user, $request);

        } catch (IncompletePayment $e) {
            return redirect()->route('cashier.payment', [
                $e->payment->id,
                'redirect' => route('mypage'),
            ]);
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            return back()->with('error', '決済処理中にエラーが発生しました。カード情報をご確認のうえ、もう一度お試しください。');
        }
    }

    private function processSubscription($user, Request $request)
    {
        $priceId = config('cashier.price_id');

        if (!$priceId) {
            return back()->with('error', '定期便の価格設定が未完了です。管理者にお問い合わせください。');
        }

        // すでに有効な定期便があればスキップ
        if ($user->subscribed('default')) {
            return redirect()->route('mypage')->with('error', 'すでに定期便にご登録済みです。');
        }

        $user->newSubscription('default', $priceId)
            ->create($request->payment_method);

        return redirect()->route('mypage')
            ->with('success', '定期便のお申し込みが完了しました。初回のお届けをお待ちください。');
    }

    private function processSinglePurchase($user, Request $request)
    {
        $products = Product::whereIn('id', $request->products)->active()->get();

        if ($products->isEmpty()) {
            return back()->with('error', '商品が見つかりませんでした。');
        }

        $subtotal = $products->sum('price');
        $shipping = 550;
        $total    = ($subtotal + $shipping) * 100; // Stripe は最小通貨単位（銭）

        $user->charge($total, $request->payment_method, [
            'description' => '単品購入: ' . $products->pluck('name')->join(', '),
            'metadata'    => [
                'product_ids' => $products->pluck('id')->join(','),
                'user_id'     => $user->id,
            ],
        ]);

        return redirect()->route('mypage')
            ->with('success', 'ご購入ありがとうございます！3〜5営業日以内に発送いたします。');
    }
}
