<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

class ProductTest extends InventoryTestCase
{
    private function payload(array $override = []): array
    {
        return array_merge([
            'sku' => 'DN-100',
            'name' => 'Donat Uji',
            'category_id' => Category::firstOrCreate(['name' => 'Donat'])->id,
            'purchase_price' => 3000,
            'selling_price' => 6000,
            'stock' => 5,
            'minimum_stock' => 2,
        ], $override);
    }

    public function test_admin_can_create_product(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)->post(route('products.store'), $this->payload())
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['sku' => 'DN-100', 'stock' => 5]);
    }

    public function test_duplicate_sku_is_rejected(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $this->makeProduct(5, ['sku' => 'DN-100']);

        $this->actingAs($admin)->post(route('products.store'), $this->payload())
            ->assertSessionHasErrors('sku');

        $this->assertSame(1, Product::where('sku', 'DN-100')->count());
    }

    public function test_negative_price_is_rejected(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)->post(route('products.store'), $this->payload(['selling_price' => -1]))
            ->assertSessionHasErrors('selling_price');
    }

    public function test_search_and_pagination_return_matching_data(): void
    {
        $user = $this->makeUser();
        for ($i = 1; $i <= 12; $i++) {
            $this->makeProduct(5, ['name' => "Donat Rasa {$i}"]);
        }
        $this->makeProduct(5, ['name' => 'Kopi Susu']);

        $this->actingAs($user)->get(route('products.index', ['q' => 'Kopi']))
            ->assertOk()->assertSee('Kopi Susu')->assertDontSee('Donat Rasa 1');

        $this->actingAs($user)->get(route('products.index', ['q' => 'Donat']))
            ->assertOk()->assertSee('page=2', false); // filter dipertahankan di link pagination
    }

    public function test_product_used_in_transaction_cannot_be_deleted(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $product = $this->makeProduct(5);

        $this->actingAs($admin)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $this->actingAs($admin)->delete(route('products.destroy', $product))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}