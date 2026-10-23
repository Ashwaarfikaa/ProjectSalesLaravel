<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\User;

class TransactionTest extends InventoryTestCase
{
    public function test_successful_purchase_increases_stock(): void
    {
        $staff = $this->makeUser();
        $product = $this->makeProduct(10);
        $supplier = $this->makeSupplier();

        $this->actingAs($staff)->post(route('purchases.store'), [
            'supplier_id' => $supplier->id,
            'items' => [['product_id' => $product->id, 'quantity' => 15, 'unit_cost' => 3000]],
        ])->assertSessionHasNoErrors();

        $this->assertSame(25, $product->fresh()->stock);
        $this->assertDatabaseHas('purchases', ['supplier_id' => $supplier->id, 'user_id' => $staff->id, 'total' => 45000]);
    }

    public function test_successful_sale_decreases_stock_and_total_is_computed_by_server(): void
    {
        $staff = $this->makeUser();
        $product = $this->makeProduct(10, ['selling_price' => 5000]);

        $this->actingAs($staff)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 3]],
            'total' => 1, // input palsu dari browser harus diabaikan
        ])->assertSessionHasNoErrors();

        $this->assertSame(7, $product->fresh()->stock);
        $this->assertEquals(15000, Sale::first()->total);
    }

    public function test_sale_with_insufficient_stock_is_rejected_without_changes(): void
    {
        $staff = $this->makeUser();
        $product = $this->makeProduct(4);

        $this->actingAs($staff)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 6]],
        ])->assertSessionHasErrors('items');

        $this->assertSame(4, $product->fresh()->stock);
        $this->assertSame(0, Sale::count());
    }

    public function test_failure_on_one_item_rolls_back_the_whole_sale(): void
    {
        $staff = $this->makeUser();
        $ok = $this->makeProduct(10);
        $short = $this->makeProduct(1);

        $this->actingAs($staff)->post(route('sales.store'), [
            'items' => [
                ['product_id' => $ok->id, 'quantity' => 3],
                ['product_id' => $short->id, 'quantity' => 5],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertSame(10, $ok->fresh()->stock);
        $this->assertSame(1, $short->fresh()->stock);
        $this->assertSame(0, Sale::count());
        $this->assertDatabaseCount('sale_items', 0);
    }

    public function test_invalid_quantity_is_rejected(): void
    {
        $staff = $this->makeUser();
        $product = $this->makeProduct(10);

        $this->actingAs($staff)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 0]],
        ])->assertSessionHasErrors('items.0.quantity');
    }

    public function test_admin_cancel_restores_stock(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $product = $this->makeProduct(10);

        $this->actingAs($admin)->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 4]],
        ]);
        $this->assertSame(6, $product->fresh()->stock);

        $sale = Sale::first();
        $this->actingAs($admin)->post(route('sales.cancel', $sale))->assertSessionHasNoErrors();

        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(Sale::STATUS_CANCELLED, $sale->fresh()->status);

        // pembatalan kedua tidak boleh memulihkan stok lagi
        $this->actingAs($admin)->post(route('sales.cancel', $sale))->assertSessionHasErrors('status');
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_sales_can_be_filtered_by_status(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $product = $this->makeProduct(20);

        $this->actingAs($admin)->post(route('sales.store'), ['items' => [['product_id' => $product->id, 'quantity' => 1]]]);
        $this->actingAs($admin)->post(route('sales.store'), ['items' => [['product_id' => $product->id, 'quantity' => 1]]]);
        $this->actingAs($admin)->post(route('sales.cancel', Sale::first()));

        $cancelled = Sale::where('status', 'cancelled')->first();
        $completed = Sale::where('status', 'completed')->first();

        $this->actingAs($admin)->get(route('sales.index', ['status' => 'cancelled']))
            ->assertOk()->assertSee($cancelled->sale_number)->assertDontSee($completed->sale_number);
    }
}