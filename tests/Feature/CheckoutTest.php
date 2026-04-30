<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_requires_auth(): void
    {
        $this->get(route('checkout'))
            ->assertRedirect(route('login'));
    }

    public function test_checkout_page_loads_for_unverified_user(): void
    {
        /** @var User $user */
        $user    = User::factory()->unverified()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get(route('checkout', ['products' => [$product->id], 'type' => 'single']))
            ->assertStatus(200);
    }

    public function test_checkout_page_loads_for_verified_user(): void
    {
        /** @var User $user */
        $user    = User::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get(route('checkout', ['products' => [$product->id], 'type' => 'single']))
            ->assertStatus(200);
    }

    public function test_checkout_process_requires_verified_email(): void
    {
        /** @var User $user */
        $user    = User::factory()->unverified()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->post(route('checkout.process'), [
                'payment_method' => 'pm_test',
                'products'       => [$product->id],
                'type'           => 'single',
                'postal_code'    => '150-0001',
                'address'        => '東京都渋谷区',
            ])
            ->assertSessionHas('error');
    }

    public function test_checkout_process_validates_product_ids(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('checkout.process'), [
                'payment_method' => 'pm_test',
                'products'       => [99999], // 存在しないID
                'type'           => 'single',
                'postal_code'    => '150-0001',
                'address'        => '東京都渋谷区',
            ])
            ->assertSessionHasErrors('products.*');
    }

    public function test_checkout_process_rejects_inactive_products(): void
    {
        /** @var User $user */
        $user    = User::factory()->create();
        $product = Product::factory()->inactive()->create();

        $this->actingAs($user)
            ->post(route('checkout.process'), [
                'payment_method' => 'pm_test',
                'products'       => [$product->id],
                'type'           => 'single',
                'postal_code'    => '150-0001',
                'address'        => '東京都渋谷区',
            ])
            // exists:products,id はis_activeを見ないためバリデーション通過、
            // controller内のactive()スコープで弾かれ error flash になる
            ->assertSessionHas('error');
    }

    public function test_checkout_process_requires_all_fields(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('checkout.process'), [])
            ->assertSessionHasErrors(['payment_method', 'products', 'type', 'postal_code', 'address']);
    }
}
