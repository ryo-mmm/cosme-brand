<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $productIds = $request->input('products', []);
        $products = Product::whereIn('id', $productIds)->active()->get();
        $type = $request->input('type', 'subscription'); // subscription | single

        $intent = null;
        if (auth()->check()) {
            $intent = auth()->user()->createSetupIntent();
        }

        return view('checkout', compact('products', 'intent', 'type'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', 'string'],
            'products'       => ['required', 'array'],
            'type'           => ['required', 'in:subscription,single'],
        ]);

        $user = auth()->user();
        $user->createOrGetStripeCustomer();
        $user->updateDefaultPaymentMethod($request->payment_method);

        if ($request->type === 'subscription') {
            // 定期便：実環境では Stripe Price ID を指定
            // $user->newSubscription('default', 'price_xxxxx')->create($request->payment_method);
            return redirect()->route('mypage')->with('success', 'ご購読ありがとうございます！定期便が開始されました。');
        }

        // 単品購入：実環境では Stripe PaymentIntent を作成
        // \Stripe\PaymentIntent::create([...]);
        return redirect()->route('mypage')->with('success', 'ご購入ありがとうございます！商品を発送いたします。');
    }
}
