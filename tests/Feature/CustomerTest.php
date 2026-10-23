<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;

class CustomerTest extends InventoryTestCase
{
    public function test_staff_can_add_customer(): void
    {
        $staff = $this->makeUser();

        $this->actingAs($staff)->post(route('customers.store'), [
            'name' => 'Budi Santoso', 'phone' => '0813', 'email' => 'budi@example.test',
        ])->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', ['name' => 'Budi Santoso']);
    }

    public function test_customer_name_is_required_and_email_must_be_valid(): void
    {
        $staff = $this->makeUser();

        $this->actingAs($staff)->post(route('customers.store'), ['name' => '', 'email' => 'bukan-email'])
            ->assertSessionHasErrors(['name', 'email']);
    }

    public function test_customer_search_works(): void
    {
        $staff = $this->makeUser();
        Customer::create(['name' => 'Budi Santoso']);
        Customer::create(['name' => 'Siti Rahmawati']);

        $this->actingAs($staff)->get(route('customers.index', ['q' => 'Siti']))
            ->assertOk()->assertSee('Siti Rahmawati')->assertDontSee('Budi Santoso');
    }

    public function test_sale_can_be_linked_to_a_customer(): void
    {
        $staff = $this->makeUser();
        $customer = Customer::create(['name' => 'Budi Santoso']);
        $product = $this->makeProduct(10);

        $this->actingAs($staff)->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ])->assertSessionHasNoErrors();

        $this->assertSame($customer->id, Sale::first()->customer_id);
        $this->actingAs($staff)->get(route('customers.show', $customer))->assertOk()->assertSee(Sale::first()->sale_number);
    }

    public function test_sale_without_customer_is_still_allowed(): void
    {
        $staff = $this->makeUser();
        $product = $this->makeProduct(10);

        $this->actingAs($staff)->post(route('sales.store'), [
            'customer_id' => '',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertSessionHasNoErrors();

        $this->assertNull(Sale::first()->customer_id);
    }

    public function test_customer_with_sales_cannot_be_deleted_and_staff_cannot_delete(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $staff = $this->makeUser();
        $customer = Customer::create(['name' => 'Budi Santoso']);
        $product = $this->makeProduct(10);

        $this->actingAs($staff)->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $this->actingAs($staff)->delete(route('customers.destroy', $customer))->assertForbidden();
        $this->actingAs($admin)->delete(route('customers.destroy', $customer))->assertSessionHas('error');

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }
}