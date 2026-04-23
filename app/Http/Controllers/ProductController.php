<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active();

        if ($request->filled('skin_type')) {
            $query->forSkinType($request->skin_type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->paginate(12);
        return view('products.index', compact('products'));
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->active()->firstOrFail();
        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->whereJsonContains('skin_types', $product->skin_types[0] ?? 'dry')
            ->take(3)
            ->get();

        return view('products.show', compact('product', 'related'));
    }
}
