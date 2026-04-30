<?php

namespace App\Http\Controllers;

use App\Models\SkinDiagnosis;

class DiagnosisController extends Controller
{
    public function index()
    {
        return view('diagnosis');
    }

    public function result(string $id)
    {
        $diagnosis = SkinDiagnosis::findOrFail($id);

        if ($diagnosis->user_id) {
            abort_if(!auth()->check() || $diagnosis->user_id !== auth()->id(), 403);
        } else {
            abort_if($diagnosis->session_id !== session()->getId(), 403);
        }

        $products = $diagnosis->recommendedProducts();
        return view('diagnosis-result', compact('diagnosis', 'products'));
    }
}
