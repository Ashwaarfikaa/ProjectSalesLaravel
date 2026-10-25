<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users Demo
        User::firstOrCreate(['email' => 'admin@summitgear.com'], [
            'name'     => 'Administrator Owner',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::firstOrCreate(['email' => 'staff@summitgear.com'], [
            'name'     => 'Staff Kasir',
            'password' => Hash::make('password'),
            'role'     => 'staff',
        ]);

        // Categories Demo
        $cat1 = Category::firstOrCreate(['name' => 'Tenda & Shelter']);
        $cat2 = Category::firstOrCreate(['name' => 'Tas & Carrier']);
        $cat3 = Category::firstOrCreate(['name' => 'Perlengkapan Masak']);
        $cat4 = Category::firstOrCreate(['name' => 'Sepatu & Sandal']);

        // Suppliers Demo
        Supplier::firstOrCreate(['name' => 'PT Eigerindo Multi Produk'], [
            'phone'   => '081234567890',
            'email'   => 'supplier@eiger.co.id',
            'address' => 'Bandung, Jawa Barat',
        ]);

        // Products Demo
        Product::firstOrCreate(['sku' => 'SKU-TND-001'], [
            'category_id'    => $cat1->id,
            'name'           => 'Tenda Dome Eiger 4 Person',
            'purchase_price' => 850000,
            'selling_price'  => 1200000,
            'stock'          => 10,
            'minimum_stock'  => 3,
        ]);

        Product::firstOrCreate(['sku' => 'SKU-CAR-001'], [
            'category_id'    => $cat2->id,
            'name'           => 'Carrier Consina Tarebbi 60L',
            'purchase_price' => 500000,
            'selling_price'  => 750000,
            'stock'          => 2,
            'minimum_stock'  => 5,
        ]);
    }
}