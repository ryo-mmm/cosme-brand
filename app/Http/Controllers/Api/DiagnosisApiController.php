<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiagnosisQuestion;
use App\Models\Product;
use App\Models\SkinDiagnosis;
use App\Services\DiagnosisService;
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

    public function submit(Request $request, DiagnosisService $service)
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, DiagnosisQuestion> $questions */
        $questions = DiagnosisQuestion::active()->get();

        // 設問ごとに選択肢インデックスの上限を動的に決定して厳密バリデーション
        $rules = [
            'answers' => ['required', 'array', 'size:' . $questions->count()],
        ];
        foreach ($questions as $question) {
            $maxIndex = count($question->options) - 1;
            $rules["answers.{$question->id}"] = ['required', 'integer', 'min:0', 'max:' . $maxIndex];
        }
        $request->validate($rules);

        $result   = $service->analyze($questions, $request->answers);
        $products = Product::active()->forSkinType($result['skin_type'])->get();

        $diagnosis = SkinDiagnosis::create([
            'user_id'                 => auth()->id(),
            'session_id'              => session()->getId(),
            'answers'                 => $request->answers, // [questionId => optionIndex]
            'skin_type'               => $result['skin_type'],
            'score'                   => $result['total_score'],
            'recommended_product_ids' => $products->pluck('id')->toArray(),
        ]);

        return response()->json([
            'diagnosis_id'    => $diagnosis->id,
            'skin_type'       => $result['skin_type'],
            'skin_type_label' => $diagnosis->skin_type_label,
            'products'        => $products,
        ]);
    }
}
