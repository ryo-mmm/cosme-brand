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

        $intent = null;
        if (auth()->check()) {
            $intent = auth()->user()->createSetupIntent();
        }

        return view('checkout', compact('products', 'intent'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', 'string'],
            'products' => ['required', 'array'],
        ]);

        $user = auth()->user();
        $user->createOrGetStripeCustomer();
        $user->updateDefaultPaymentMethod($request->payment_method);

        // サブスクリプション作成（実環境ではStripe Price IDが必要）
        // $user->newSubscription('default', 'price_xxxxx')->create($request->payment_method);

        return redirect()->route('mypage')->with('success', 'ご購読ありがとうございます！定期便が開始されました。');
    }
}
