<?php

namespace Tests\Unit;

use App\Services\DiagnosisService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

/**
 * DiagnosisService の純粋ロジックを検証するユニットテスト。
 * DB・フレームワーク起動不要のため PHPUnit\Framework\TestCase を直接使用。
 */
class DiagnosisServiceTest extends TestCase
{
    private DiagnosisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DiagnosisService();
    }

    // ─── ヘルパー ────────────────────────────────────────────────────────────

    /** @param array<int, array{label:string, score:int}> $options */
    private function makeQuestion(int $id, int $order, array $options): object
    {
        return (object) compact('id', 'order', 'options');
    }

    /** 標準7問構成（シーダーと同じスコア体系）を返す */
    private function makeStandardQuestions(): Collection
    {
        return collect([
            $this->makeQuestion(1, 1, [
                ['label' => 'すぐにつっぱる',       'score' => 0],
                ['label' => '少しつっぱる',           'score' => 1],
                ['label' => 'べたつく',               'score' => 3],
                ['label' => 'ほとんど変わらない',     'score' => 2],
            ]),
            $this->makeQuestion(2, 2, [
                ['label' => 'サラサラしている',       'score' => 0],
                ['label' => '少し光る',               'score' => 2],
                ['label' => 'かなりテカる',           'score' => 3],
                ['label' => 'ゾーンによって異なる',   'score' => 2],
            ]),
            $this->makeQuestion(3, 3, [
                ['label' => '乾燥・粉ふき',           'score' => 0],
                ['label' => 'ニキビ・吹き出物',       'score' => 3],
                ['label' => '赤み・かぶれ',           'score' => 4], // index 2 → sensitive trigger
                ['label' => '毛穴の黒ずみ',           'score' => 3],
            ]),
            $this->makeQuestion(4, 4, [
                ['label' => 'すぐに乾燥する',         'score' => 0],
                ['label' => 'ニキビが増える',         'score' => 3],
                ['label' => '敏感に反応する',         'score' => 4],
                ['label' => 'あまり変化しない',       'score' => 2],
            ]),
            $this->makeQuestion(5, 5, [
                ['label' => 'すぐに乾く',             'score' => 0],
                ['label' => 'ちょうどよい',           'score' => 2],
                ['label' => 'べたつく',               'score' => 3],
                ['label' => '刺激を感じることがある', 'score' => 4],
            ]),
            $this->makeQuestion(6, 6, [
                ['label' => '目立たない',             'score' => 0],
                ['label' => '少し気になる',           'score' => 2],
                ['label' => 'かなり目立つ',           'score' => 3],
                ['label' => '赤みを伴う',             'score' => 4],
            ]),
            $this->makeQuestion(7, 7, [
                ['label' => '3分以内',                'score' => 2],
                ['label' => '5〜10分',                'score' => 2],
                ['label' => '10分以上',               'score' => 2],
                ['label' => 'できるだけ短く',         'score' => 2],
            ]),
        ]);
    }

    // ─── classify() ──────────────────────────────────────────────────────────

    public function test_classify_returns_dry_at_score_boundary_4(): void
    {
        $this->assertSame('dry', $this->service->classify(4, []));
    }

    public function test_classify_returns_dry_for_all_zero_scores(): void
    {
        $this->assertSame('dry', $this->service->classify(0, []));
    }

    public function test_classify_returns_combination_at_score_5(): void
    {
        $this->assertSame('combination', $this->service->classify(5, []));
    }

    public function test_classify_returns_combination_at_score_boundary_9(): void
    {
        $this->assertSame('combination', $this->service->classify(9, []));
    }

    public function test_classify_returns_oily_at_score_10(): void
    {
        $this->assertSame('oily', $this->service->classify(10, []));
    }

    public function test_classify_returns_oily_for_high_score(): void
    {
        $this->assertSame('oily', $this->service->classify(20, []));
    }

    public function test_classify_returns_sensitive_when_q3_score_is_4(): void
    {
        // 第3問で score=4（赤み・かぶれ）→ 合計スコアに関わらず敏感肌
        $this->assertSame('sensitive', $this->service->classify(4,  [3 => 4]));
        $this->assertSame('sensitive', $this->service->classify(10, [3 => 4]));
        $this->assertSame('sensitive', $this->service->classify(0,  [3 => 4]));
    }

    public function test_classify_does_not_trigger_sensitive_from_other_questions(): void
    {
        // Q4, Q5, Q6 の score=4 は合計スコアに加算されるが敏感肌トリガーではない
        $this->assertSame('oily', $this->service->classify(12, [4 => 4, 5 => 4, 6 => 4]));
    }

    public function test_classify_sensitive_takes_precedence_over_oily_score(): void
    {
        // Q3=4 かつ 高合計スコア → 敏感肌優先
        $this->assertSame('sensitive', $this->service->classify(25, [3 => 4]));
    }

    public function test_classify_q3_score_3_does_not_trigger_sensitive(): void
    {
        // Q3 で score=3（ニキビ・吹き出物）→ 敏感肌にはならない
        $this->assertSame('combination', $this->service->classify(9, [3 => 3]));
    }

    // ─── resolveScores() ─────────────────────────────────────────────────────

    public function test_resolve_scores_maps_option_index_to_score(): void
    {
        $questions = collect([
            $this->makeQuestion(1, 1, [
                ['label' => 'A', 'score' => 0],
                ['label' => 'B', 'score' => 2],
                ['label' => 'C', 'score' => 3],
            ]),
        ]);

        $scores = $this->service->resolveScores($questions, [1 => 1]); // index 1 → score 2
        $this->assertSame([1 => 2], $scores);
    }

    public function test_resolve_scores_handles_first_option(): void
    {
        $questions = collect([
            $this->makeQuestion(5, 1, [['label' => 'X', 'score' => 0], ['label' => 'Y', 'score' => 3]]),
        ]);

        $scores = $this->service->resolveScores($questions, [5 => 0]);
        $this->assertSame([5 => 0], $scores);
    }

    public function test_resolve_scores_handles_last_option(): void
    {
        $questions = collect([
            $this->makeQuestion(2, 1, [
                ['label' => 'A', 'score' => 0],
                ['label' => 'B', 'score' => 1],
                ['label' => 'C', 'score' => 2],
                ['label' => 'D', 'score' => 4],
            ]),
        ]);

        $scores = $this->service->resolveScores($questions, [2 => 3]); // index 3 → score 4
        $this->assertSame([2 => 4], $scores);
    }

    public function test_resolve_scores_handles_multiple_questions(): void
    {
        $questions = collect([
            $this->makeQuestion(1, 1, [['label' => 'A', 'score' => 0], ['label' => 'B', 'score' => 3]]),
            $this->makeQuestion(2, 2, [['label' => 'C', 'score' => 1], ['label' => 'D', 'score' => 2]]),
        ]);

        $scores = $this->service->resolveScores($questions, [1 => 1, 2 => 0]);
        $this->assertSame([1 => 3, 2 => 1], $scores);
    }

    // ─── analyze() ───────────────────────────────────────────────────────────

    public function test_analyze_dry_result_for_all_minimum_scores(): void
    {
        $questions = $this->makeStandardQuestions();
        // 全問インデックス0 → Q7 のみ score=2、他は score=0 → 合計2
        $answers = $questions->mapWithKeys(fn($q) => [$q->id => 0])->all();

        $result = $this->service->analyze($questions, $answers);

        $this->assertSame('dry', $result['skin_type']);
        $this->assertSame(2, $result['total_score']); // Q7のみ score=2
    }

    public function test_analyze_sensitive_result_when_q3_index_2_selected(): void
    {
        $questions = $this->makeStandardQuestions();
        $answers   = $questions->mapWithKeys(fn($q) => [$q->id => 0])->all();
        // Q3(id=3) でインデックス2（赤み・かぶれ, score=4）を選択
        $answers[3] = 2;

        $result = $this->service->analyze($questions, $answers);

        $this->assertSame('sensitive', $result['skin_type']);
    }

    public function test_analyze_oily_result_for_high_score_selections(): void
    {
        $questions = $this->makeStandardQuestions();
        // 各問で最高スコア選択（ただしQ3はscore=3にして sensitive 回避）
        $answers = [
            1 => 2, // score=3
            2 => 2, // score=3
            3 => 1, // score=3 (index 1: ニキビ・吹き出物, not 赤み)
            4 => 2, // score=4
            5 => 2, // score=3
            6 => 2, // score=3
            7 => 0, // score=2
        ];

        $result = $this->service->analyze($questions, $answers);

        $this->assertSame('oily', $result['skin_type']);
        $this->assertGreaterThan(9, $result['total_score']);
    }

    public function test_analyze_combination_result_for_mid_range_scores(): void
    {
        $questions = $this->makeStandardQuestions();
        // 中間スコアを狙った選択
        $answers = [
            1 => 1, // score=1
            2 => 1, // score=2
            3 => 0, // score=0
            4 => 0, // score=0
            5 => 1, // score=2
            6 => 1, // score=2
            7 => 0, // score=2
        ];

        $result = $this->service->analyze($questions, $answers);

        $this->assertSame('combination', $result['skin_type']);
        $this->assertSame(9, $result['total_score']);
    }

    public function test_analyze_returns_total_score(): void
    {
        $questions = collect([
            $this->makeQuestion(1, 1, [['label' => 'A', 'score' => 3]]),
            $this->makeQuestion(2, 2, [['label' => 'B', 'score' => 2]]),
        ]);

        $result = $this->service->analyze($questions, [1 => 0, 2 => 0]);

        $this->assertSame(5, $result['total_score']);
    }
}
