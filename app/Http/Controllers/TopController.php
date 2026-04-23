<?php

namespace App\Http\Controllers;

use App\Models\Product;

class TopController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::active()->take(3)->get();
        return view('top', compact('featuredProducts'));
    }
}
