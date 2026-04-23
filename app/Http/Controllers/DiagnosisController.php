<?php

namespace App\Http\Controllers;

use App\Models\SkinDiagnosis;
use App\Models\Product;

class DiagnosisController extends Controller
{
    public function index()
    {
        return view('diagnosis');
    }

    public function result(string $id)
    {
        $diagnosis = SkinDiagnosis::findOrFail($id);
        $products = $diagnosis->recommendedProducts();
        return view('diagnosis-result', compact('diagnosis', 'products'));
    }
}
