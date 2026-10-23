<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\User;

class AuthorizationTest extends InventoryTestCase
{
    public function test_staff_cannot_access_admin_functions_by_direct_url(): void
    {
        $staff = $this->makeUser();
        $product = $this->makeProduct(5);

        $this->actingAs($staff)->get(route('categories.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('suppliers.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('users.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('products.create'))->assertForbidden();
        $this->actingAs($staff)->post(route('products.store'), [])->assertForbidden();
        $this->actingAs($staff)->delete(route('products.destroy', $product))->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_staff_can_view_products_and_create_transactions(): void
    {
        $staff = $this->makeUser();
        $product = $this->makeProduct(5);

        $this->actingAs($staff)->get(route('products.index'))->assertOk();
        $this->actingAs($staff)->get(route('products.show', $product))->assertOk();
        $this->actingAs($staff)->get(route('sales.create'))->assertOk();
        $this->actingAs($staff)->get(route('purchases.create'))->assertOk();
    }

    public function test_staff_cannot_cancel_transactions(): void
    {
        $staff = $this->makeUser();
        $product = $this->makeProduct(5);

        $this->actingAs($staff)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ]);
        $sale = Sale::first();

        $this->actingAs($staff)->post(route('sales.cancel', $sale))->assertForbidden();

        $this->assertSame(Sale::STATUS_COMPLETED, $sale->fresh()->status);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_staff_cannot_view_another_users_sale(): void
    {
        $owner = $this->makeUser();
        $other = $this->makeUser();
        $product = $this->makeProduct(5);

        $this->actingAs($owner)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);
        $sale = Sale::first();

        $this->actingAs($other)->get(route('sales.show', $sale))->assertForbidden();
        $this->actingAs($owner)->get(route('sales.show', $sale))->assertOk();
        $this->actingAs($this->makeUser(User::ROLE_ADMIN))->get(route('sales.show', $sale))->assertOk();
    }

    public function test_admin_can_access_master_data_and_users(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)->get(route('categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('suppliers.index'))->assertOk();
        $this->actingAs($admin)->get(route('users.index'))->assertOk();
    }
}