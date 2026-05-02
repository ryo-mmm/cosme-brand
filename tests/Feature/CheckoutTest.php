<?php

namespace Tests\Feature;

use App\Http\Controllers\CheckoutController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    // ─── HTTP layer tests ─────────────────────────────────────────────────────

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

    // ─── reserveStock unit tests (Reflection) ─────────────────────────────────

    /** @return mixed */
    private function callReserveStock(array $uniqueIds, array $quantityMap): mixed
    {
        $method = new ReflectionMethod(CheckoutController::class, 'reserveStock');
        $method->setAccessible(true);
        return $method->invoke(new CheckoutController(), $uniqueIds, $quantityMap);
    }

    public function test_reserve_stock_returns_null_and_decrements_on_success(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'stock' => 5]);

        $error = $this->callReserveStock([$product->id], [$product->id => 2]);

        $this->assertNull($error);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    }

    public function test_reserve_stock_returns_error_for_zero_stock(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'stock' => 0]);

        $error = $this->callReserveStock([$product->id], [$product->id => 1]);

        $this->assertNotNull($error);
        $this->assertStringContainsString('在庫切れ', $error);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 0]);
    }

    public function test_reserve_stock_returns_error_when_quantity_exceeds_stock(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'stock' => 1]);

        $error = $this->callReserveStock([$product->id], [$product->id => 3]);

        $this->assertNotNull($error);
        $this->assertStringContainsString('在庫が不足', $error);
        // 在庫は変化していないこと
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
    }

    public function test_reserve_stock_handles_multiple_products(): void
    {
        $p1 = Product::factory()->create(['is_active' => true, 'stock' => 10]);
        $p2 = Product::factory()->create(['is_active' => true, 'stock' => 3]);

        $quantityMap = [$p1->id => 4, $p2->id => 2];
        $error = $this->callReserveStock([$p1->id, $p2->id], $quantityMap);

        $this->assertNull($error);
        $this->assertDatabaseHas('products', ['id' => $p1->id, 'stock' => 6]);
        $this->assertDatabaseHas('products', ['id' => $p2->id, 'stock' => 1]);
    }

    public function test_reserve_stock_rolls_back_all_on_partial_failure(): void
    {
        $p1 = Product::factory()->create(['is_active' => true, 'stock' => 10]);
        $p2 = Product::factory()->create(['is_active' => true, 'stock' => 0]); // 在庫切れ

        $quantityMap = [$p1->id => 1, $p2->id => 1];
        $error = $this->callReserveStock([$p1->id, $p2->id], $quantityMap);

        $this->assertNotNull($error);
        // p1 の在庫もロールバックされていること
        $this->assertDatabaseHas('products', ['id' => $p1->id, 'stock' => 10]);
        $this->assertDatabaseHas('products', ['id' => $p2->id, 'stock' => 0]);
    }

    public function test_reserve_stock_returns_error_for_inactive_product(): void
    {
        $product = Product::factory()->inactive()->create(['stock' => 10]);

        $error = $this->callReserveStock([$product->id], [$product->id => 1]);

        $this->assertNotNull($error);
        $this->assertStringContainsString('見つかりませんでした', $error);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
    }
}
