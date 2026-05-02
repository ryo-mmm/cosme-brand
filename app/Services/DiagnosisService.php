<?php

namespace App\Services;

use Illuminate\Support\Collection;

class DiagnosisService
{
    /**
     * 質問コレクションとオプション選択インデックスからスコアを解決する。
     *
     * @param  Collection $questions  id / order / options を持つオブジェクトのコレクション
     * @param  array      $answers    [questionId => optionIndex (0-based)]
     * @return array                  [questionId => score]
     */
    public function resolveScores(Collection $questions, array $answers): array
    {
        $scores = [];
        foreach ($questions as $question) {
            $idx = (int) ($answers[$question->id] ?? 0);
            $scores[$question->id] = (int) ($question->options[$idx]['score'] ?? 0);
        }
        return $scores;
    }

    /**
     * 合計スコアと設問別スコアから肌タイプを判定する（純粋関数）。
     *
     * @param  int   $totalScore     全設問の合計スコア
     * @param  array $scoresByOrder  [questionOrder => score]（1-based order）
     * @return string                'dry' | 'oily' | 'combination' | 'sensitive'
     */
    public function classify(int $totalScore, array $scoresByOrder): string
    {
        // 第3問「肌トラブル」で score=4（赤み・かぶれ）を選択 → 敏感肌
        if (($scoresByOrder[3] ?? 0) === 4) {
            return 'sensitive';
        }

        if ($totalScore <= 4)  return 'dry';
        if ($totalScore <= 9)  return 'combination';
        return 'oily';
    }

    /**
     * 回答を受け取り肌タイプ判定を行う。
     *
     * @param  Collection $questions  active() orderBy('order') で取得した質問
     * @param  array      $answers    [questionId => optionIndex]
     * @return array                  ['skin_type' => string, 'total_score' => int]
     */
    public function analyze(Collection $questions, array $answers): array
    {
        $scoresByQuestionId = $this->resolveScores($questions, $answers);
        $totalScore         = (int) array_sum($scoresByQuestionId);

        $scoresByOrder = [];
        foreach ($questions as $question) {
            $scoresByOrder[(int) $question->order] = $scoresByQuestionId[$question->id];
        }

        return [
            'skin_type'   => $this->classify($totalScore, $scoresByOrder),
            'total_score' => $totalScore,
        ];
    }
}
