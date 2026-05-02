<?php

namespace Tests\Feature;

use App\Models\DiagnosisQuestion;
use App\Models\SkinDiagnosis;
use App\Models\User;
use Database\Seeders\DiagnosisQuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiagnosisTest extends TestCase
{
    use RefreshDatabase;

    // ─── 診断ページ・結果ページ ───────────────────────────────────────────────

    public function test_diagnosis_index_is_accessible_by_guests(): void
    {
        $this->get(route('diagnosis'))->assertStatus(200);
    }

    public function test_diagnosis_result_requires_ownership_for_user_diagnosis(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $other */
        $other = User::factory()->create();

        $diagnosis = SkinDiagnosis::factory()->create([
            'user_id'                 => $owner->id,
            'skin_type'               => 'dry',
            'recommended_product_ids' => [],
        ]);

        $this->actingAs($owner)->get(route('diagnosis.result', $diagnosis->id))->assertStatus(200);
        $this->actingAs($other)->get(route('diagnosis.result', $diagnosis->id))->assertStatus(403);
    }

    public function test_guest_diagnosis_result_requires_matching_session(): void
    {
        $diagnosis = SkinDiagnosis::factory()->guest()->create([
            'session_id'              => 'session-abc',
            'skin_type'               => 'oily',
            'recommended_product_ids' => [],
        ]);

        $this->get(route('diagnosis.result', $diagnosis->id))->assertStatus(403);
    }

    // ─── 質問取得 API ────────────────────────────────────────────────────────

    public function test_diagnosis_api_returns_questions(): void
    {
        $this->getJson('/api/diagnosis/questions')
            ->assertStatus(200)
            ->assertJsonStructure(['questions']);
    }

    // ─── 診断送信 API（正常系） ──────────────────────────────────────────────

    public function test_diagnosis_api_submit_returns_result(): void
    {
        $this->seed(DiagnosisQuestionSeeder::class);

        // 全問インデックス0（最低スコア選択）で送信
        $answers = DiagnosisQuestion::all()->mapWithKeys(fn($q) => [$q->id => 0])->all();

        $this->postJson('/api/diagnosis/submit', ['answers' => $answers])
            ->assertStatus(200)
            ->assertJsonStructure(['diagnosis_id', 'skin_type', 'skin_type_label', 'products']);
    }

    public function test_diagnosis_api_submit_classifies_dry_for_minimum_scores(): void
    {
        $this->seed(DiagnosisQuestionSeeder::class);

        $answers = DiagnosisQuestion::all()->mapWithKeys(fn($q) => [$q->id => 0])->all();

        $this->postJson('/api/diagnosis/submit', ['answers' => $answers])
            ->assertStatus(200)
            ->assertJsonPath('skin_type', 'dry'); // 全問最低スコア → 乾燥肌
    }

    public function test_diagnosis_api_submit_classifies_sensitive_when_q3_trigger_selected(): void
    {
        $this->seed(DiagnosisQuestionSeeder::class);

        $questions = DiagnosisQuestion::orderBy('order')->get();
        $answers   = $questions->mapWithKeys(fn($q) => [$q->id => 0])->all();

        // Q3（order=3）でインデックス2（赤み・かぶれ, score=4）を選択
        $q3 = $questions->firstWhere('order', 3);
        $answers[$q3->id] = 2;

        $this->postJson('/api/diagnosis/submit', ['answers' => $answers])
            ->assertStatus(200)
            ->assertJsonPath('skin_type', 'sensitive');
    }

    public function test_diagnosis_api_submit_persists_diagnosis_record(): void
    {
        $this->seed(DiagnosisQuestionSeeder::class);

        $answers = DiagnosisQuestion::all()->mapWithKeys(fn($q) => [$q->id => 0])->all();

        $response = $this->postJson('/api/diagnosis/submit', ['answers' => $answers]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('skin_diagnoses', [
            'id'        => $response->json('diagnosis_id'),
            'skin_type' => $response->json('skin_type'),
        ]);
    }

    // ─── 診断送信 API（バリデーション） ─────────────────────────────────────

    public function test_diagnosis_api_submit_rejects_missing_answers(): void
    {
        $this->seed(DiagnosisQuestionSeeder::class);

        $this->postJson('/api/diagnosis/submit', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['answers']);
    }

    public function test_diagnosis_api_submit_rejects_partial_answers(): void
    {
        $this->seed(DiagnosisQuestionSeeder::class);

        // 設問が7問あるのに3問だけ送信
        $partial = DiagnosisQuestion::orderBy('order')->take(3)
            ->get()->mapWithKeys(fn($q) => [$q->id => 0])->all();

        $this->postJson('/api/diagnosis/submit', ['answers' => $partial])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['answers']);
    }

    public function test_diagnosis_api_submit_rejects_out_of_range_option_index(): void
    {
        $this->seed(DiagnosisQuestionSeeder::class);

        $answers = DiagnosisQuestion::all()->mapWithKeys(fn($q) => [$q->id => 0])->all();

        // 最初の設問に存在しないインデックス（選択肢は4つなのでindex=4は範囲外）
        $firstId = DiagnosisQuestion::orderBy('order')->value('id');
        $answers[$firstId] = 99;

        $this->postJson('/api/diagnosis/submit', ['answers' => $answers])
            ->assertStatus(422)
            ->assertJsonValidationErrors(["answers.{$firstId}"]);
    }

    public function test_diagnosis_api_submit_rejects_negative_option_index(): void
    {
        $this->seed(DiagnosisQuestionSeeder::class);

        $answers = DiagnosisQuestion::all()->mapWithKeys(fn($q) => [$q->id => 0])->all();
        $firstId = DiagnosisQuestion::orderBy('order')->value('id');
        $answers[$firstId] = -1;

        $this->postJson('/api/diagnosis/submit', ['answers' => $answers])
            ->assertStatus(422)
            ->assertJsonValidationErrors(["answers.{$firstId}"]);
    }

    public function test_diagnosis_api_submit_rejects_non_integer_option_value(): void
    {
        $this->seed(DiagnosisQuestionSeeder::class);

        $answers = DiagnosisQuestion::all()->mapWithKeys(fn($q) => [$q->id => 0])->all();
        $firstId = DiagnosisQuestion::orderBy('order')->value('id');
        $answers[$firstId] = 'invalid';

        $this->postJson('/api/diagnosis/submit', ['answers' => $answers])
            ->assertStatus(422)
            ->assertJsonValidationErrors(["answers.{$firstId}"]);
    }
}
