<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_mypage_requires_auth(): void
    {
        $this->get(route('mypage'))
            ->assertRedirect(route('login'));
    }

    public function test_mypage_loads_for_unverified_user(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('mypage'))
            ->assertStatus(200);
    }

    public function test_mypage_loads_for_verified_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('mypage'))
            ->assertStatus(200);
    }

    public function test_skip_subscription_requires_active_subscription(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('mypage.skip'))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_cancel_subscription_requires_active_subscription(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('mypage.cancel'))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_change_plan_validates_plan_key(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('mypage.change-plan'), ['plan' => 'invalid_plan'])
            ->assertSessionHasErrors('plan');
    }

    public function test_change_plan_requires_active_subscription(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('mypage.change-plan'), ['plan' => 'monthly'])
            ->assertRedirect()
            ->assertSessionHas('error');
    }
}
