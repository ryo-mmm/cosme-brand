<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionStartedMail;
use App\Models\Product;
use App\Services\StripeErrorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Exceptions\IncompletePayment;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $productIds = $request->input('products', []);
        $products = Product::whereIn('id', $productIds)->active()->get();
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
            Log::warning('Card error during checkout', ['user_id' => $user->id, 'code' => $e->getStripeCode()]);
            return back()->with('error', StripeErrorService::toJapanese($e));
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API error during checkout', ['user_id' => $user->id, 'message' => $e->getMessage()]);
            return back()->with('error', 'Stripe処理中にエラーが発生しました。しばらくしてから再度お試しください。');
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
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

        // すでに有効な定期便があればスキップ
        if ($user->subscribed('default')) {
            return redirect()->route('mypage')->with('error', 'すでに定期便にご登録済みです。');
        }

        $user->newSubscription('default', $priceId)
            ->create($request->payment_method);

        Mail::to($user)->queue(new SubscriptionStartedMail($user));

        return redirect()->route('checkout.thanks', ['type' => 'subscription']);
    }

    private function processSinglePurchase(\App\Models\User $user, Request $request)
    {
        $products = Product::whereIn('id', $request->products)->active()->get();

        if ($products->isEmpty()) {
            return back()->with('error', '商品が見つかりませんでした。');
        }

        $subtotal = $products->sum('price');
        $shipping = config('subscription.shipping_fee');
        $total    = ($subtotal + $shipping) * 100; // Stripe は最小通貨単位（銭）

        $user->charge($total, $request->payment_method, [
            'description' => '単品購入: ' . $products->pluck('name')->join(', '),
            'metadata'    => [
                'product_ids' => $products->pluck('id')->join(','),
                'user_id'     => $user->id,
            ],
        ]);

        return redirect()->route('checkout.thanks', ['type' => 'single']);
    }
}
