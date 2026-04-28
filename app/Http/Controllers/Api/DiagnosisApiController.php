<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiagnosisQuestion;
use App\Models\Product;
use App\Models\SkinDiagnosis;
use Illuminate\Http\Request;

class DiagnosisApiController extends Controller
{
    public function questions()
    {
        $questions = DiagnosisQuestion::active()->get()->map(fn($q) => [
            'id'      => $q->id,
            'text'    => $q->text,
            'options' => $q->options,
        ]);

        return response()->json(['questions' => $questions]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'answers' => ['required', 'array', 'min:5'],
        ]);

        $totalScore = array_sum($request->answers);
        $skinType = $this->classifySkinType($totalScore, $request->answers);
        $products = Product::active()->forSkinType($skinType)->get();
        $productIds = $products->pluck('id')->toArray();

        $diagnosis = SkinDiagnosis::create([
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'answers' => $request->answers,
            'skin_type' => $skinType,
            'score' => $totalScore,
            'recommended_product_ids' => $productIds,
        ]);

        return response()->json([
            'diagnosis_id' => $diagnosis->id,
            'skin_type' => $skinType,
            'skin_type_label' => $diagnosis->skin_type_label,
            'products' => $products,
        ]);
    }

    private function classifySkinType(int $score, array $answers): string
    {
        // Q3でスコア4（赤み・かぶれ）が選ばれた場合は敏感肌
        if (isset($answers[2]) && $answers[2] === 4) {
            return 'sensitive';
        }
        if ($score <= 4) return 'dry';
        if ($score <= 9) return 'combination';
        return 'oily';
    }
}
