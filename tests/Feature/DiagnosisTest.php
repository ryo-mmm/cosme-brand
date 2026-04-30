<?php

namespace Tests\Feature;

use App\Models\SkinDiagnosis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiagnosisTest extends TestCase
{
    use RefreshDatabase;

    public function test_diagnosis_index_is_accessible_by_guests(): void
    {
        $response = $this->get(route('diagnosis'));

        $response->assertStatus(200);
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

        // 所有者はアクセス可
        $this->actingAs($owner)
            ->get(route('diagnosis.result', $diagnosis->id))
            ->assertStatus(200);

        // 他ユーザーは403
        $this->actingAs($other)
            ->get(route('diagnosis.result', $diagnosis->id))
            ->assertStatus(403);
    }

    public function test_guest_diagnosis_result_requires_matching_session(): void
    {
        $diagnosis = SkinDiagnosis::factory()->guest()->create([
            'session_id'              => 'session-abc',
            'skin_type'               => 'oily',
            'recommended_product_ids' => [],
        ]);

        // セッションIDが一致しないゲストは403
        $this->get(route('diagnosis.result', $diagnosis->id))
            ->assertStatus(403);
    }

    public function test_diagnosis_api_returns_questions(): void
    {
        $response = $this->getJson('/api/diagnosis/questions');

        $response->assertStatus(200)
            ->assertJsonStructure(['questions']);
    }

    public function test_diagnosis_api_submit_returns_result(): void
    {
        $response = $this->postJson('/api/diagnosis/submit', [
            'answers' => [1, 2, 3, 1, 2], // min:5 を満たす
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['diagnosis_id', 'skin_type', 'skin_type_label', 'products']);
    }
}
