<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

abstract class InventoryTestCase extends TestCase
{
    use RefreshDatabase;

    protected function makeUser(string $role = User::ROLE_STAFF): User
    {
        static $n = 0;
        $n++;

        return User::create([
            'name' => "User {$n}",
            'email' => "user{$n}@example.test",
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    protected function makeProduct(int $stock = 10, array $override = []): Product
    {
        static $n = 0;
        $n++;

        $category = Category::firstOrCreate(['name' => 'Donat']);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'sku' => "SKU-{$n}",
            'name' => "Produk {$n}",
            'purchase_price' => 3000,
            'selling_price' => 5000,
            'stock' => $stock,
            'minimum_stock' => 2,
        ], $override));
    }

    protected function makeSupplier(): Supplier
    {
        return Supplier::create(['name' => 'Pemasok Uji']);
    }
}