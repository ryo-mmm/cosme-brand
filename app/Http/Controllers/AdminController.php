<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users'    => User::count(),
            'subscribed'     => User::whereHas('subscriptions', fn($q) => $q->where('stripe_status', 'active'))->count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    public function products()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return view('admin.products', compact('products'));
    }

    public function productEdit(Product $product)
    {
        return view('admin.product-edit', compact('product'));
    }

    public function productUpdate(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'price'              => ['required', 'numeric', 'min:0'],
            'subscription_price' => ['required', 'numeric', 'min:0'],
            'stock'              => ['required', 'integer', 'min:0'],
            'is_active'          => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $product->update($data);

        return redirect()->route('admin.products')->with('success', '商品を更新しました。');
    }

    public function productToggle(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        return back()->with('success', $product->name . ' を' . ($product->is_active ? '公開' : '非公開') . 'にしました。');
    }

    public function users()
    {
        $users = User::with('subscriptions')->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function orders(Request $request)
    {
        $search   = trim($request->input('q', ''));
        $dateFrom = null;
        $dateTo   = null;

        try {
            if ($request->filled('date_from')) {
                $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
            }
            if ($request->filled('date_to')) {
                $dateTo = Carbon::parse($request->input('date_to'))->endOfDay();
            }
        } catch (\Exception) {
            // 不正な日付は無視
        }

        $query = User::whereNotNull('stripe_id')->latest();
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        $users = $query->take(100)->get();

        $charges = collect();
        foreach ($users as $user) {
            try {
                $userCharges = $user->charges(10);
                foreach ($userCharges as $charge) {
                    if (!$charge->paid) continue;
                    $createdAt = Carbon::createFromTimestamp($charge->created);
                    if ($dateFrom && $createdAt->lt($dateFrom)) continue;
                    if ($dateTo   && $createdAt->gt($dateTo))   continue;
                    $charges->push([
                        'user'        => $user,
                        'charge'      => $charge,
                        'amount'      => $charge->amount,
                        'description' => $charge->description ?? '定期便',
                        'created_at'  => $createdAt,
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Admin orders fetch error for user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        $charges = $charges->sortByDesc(fn($c) => $c['charge']->created)->values();

        return view('admin.orders', compact('charges', 'search', 'dateFrom', 'dateTo'));
    }
}
