<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo
        $admin = User::updateOrCreate(
            ['email' => 'admin@sweetglaze.test'],
            ['name' => 'Owner Sweet Glaze', 'password' => Hash::make('password'), 'role' => User::ROLE_ADMIN]
        );
        $staff = User::updateOrCreate(
            ['email' => 'staff@sweetglaze.test'],
            ['name' => 'Staff Kasir', 'password' => Hash::make('password'), 'role' => User::ROLE_STAFF]
        );
        $customerUser = User::updateOrCreate(
            ['email' => 'pelanggan@sweetglaze.test'],
            ['name' => 'Pelanggan Setia', 'password' => Hash::make('password'), 'role' => User::ROLE_CUSTOMER]
        );
        

        // Kategori
        $categories = [];
        foreach (['Donat', 'Minuman', 'Paket'] as $name) {
            $categories[$name] = Category::firstOrCreate(['name' => $name]);
        }

        // Pelanggan
        $customers = [];
        foreach ([
            ['Budi Santoso', '081311110001', 'budi@example.test', 'Jl. Merdeka No. 10, Medan'],
            ['Siti Rahmawati', '081311110002', 'siti@example.test', 'Jl. Sudirman No. 5, Medan'],
            ['Andi Pratama', '081311110003', null, 'Jl. Gatot Subroto No. 21, Medan'],
            ['Dewi Lestari', '081311110004', 'dewi@example.test', null],
        ] as [$name, $phone, $email, $address]) {
            $customers[] = Customer::firstOrCreate(['name' => $name], [
                'phone' => $phone, 'email' => $email, 'address' => $address,
            ]);
        }

        // Produk: [sku, nama, kategori, harga beli, harga jual, stok awal, stok minimum]
        foreach ([
            ['DN-001', 'Donat Glaze Original', 'Donat', 3500, 7000, 60, 15],
            ['DN-002', 'Donat Cokelat Sprinkle', 'Donat', 4000, 8000, 48, 15],
            ['DN-003', 'Donat Stroberi', 'Donat', 4000, 8000, 40, 12],
            ['DN-004', 'Donat Keju', 'Donat', 4500, 9000, 30, 10],
            ['DN-005', 'Donat Matcha', 'Donat', 5000, 10000, 8, 10],   // stok rendah
            ['DN-006', 'Donat Tiramisu', 'Donat', 5000, 10000, 25, 10],
            ['MN-001', 'Es Teh Manis', 'Minuman', 2000, 5000, 50, 20],
            ['MN-002', 'Kopi Susu Gula Aren', 'Minuman', 7000, 16000, 35, 15],
            ['MN-003', 'Cokelat Panas', 'Minuman', 6000, 14000, 4, 10], // stok rendah
            ['MN-004', 'Air Mineral 600 ml', 'Minuman', 2000, 4000, 80, 24],
            ['PK-001', 'Box Donat Isi 6', 'Paket', 20000, 42000, 20, 8],
            ['PK-002', 'Box Donat Isi 12', 'Paket', 38000, 80000, 12, 6],
        ] as [$sku, $name, $category, $buy, $sell, $stock, $min]) {
            Product::firstOrCreate(['sku' => $sku], [
                'category_id' => $categories[$category]->id,
                'name' => $name,
                'purchase_price' => $buy,
                'selling_price' => $sell,
                'stock' => $stock,
                'minimum_stock' => $min,
            ]);
        }

        // Hindari menggandakan transaksi contoh bila seeder dijalankan ulang
        if (Sale::exists()) {
            return;
        }

        $id = fn (string $sku) => Product::where('sku', $sku)->value('id');

        // Contoh penjualan 7 hari terakhir
        $sales = app(SaleService::class);
        $plans = [
            6 => [['DN-001', 6], ['MN-001', 4]],
            5 => [['DN-002', 8], ['MN-002', 3]],
            4 => [['DN-001', 10], ['PK-001', 2]],
            3 => [['DN-003', 6], ['MN-004', 10]],
            2 => [['DN-004', 5], ['MN-002', 6], ['DN-001', 4]],
            1 => [['DN-006', 7], ['PK-002', 2]],
            0 => [['DN-002', 9], ['MN-001', 8], ['PK-001', 1]],
        ];

        foreach ($plans as $daysAgo => $lines) {
            $cashier = $daysAgo % 2 === 0 ? $admin : $staff;
            $customerId = $daysAgo % 3 === 0 ? null : $customers[$daysAgo % count($customers)]->id;

            $sales->create($cashier, array_map(
                fn ($l) => ['product_id' => $id($l[0]), 'quantity' => $l[1]],
                $lines
            ), now()->subDays($daysAgo)->setTime(11, 30), $customerId);
        }

        // Satu transaksi dibatalkan sebagai contoh filter status
        $cancelled = $sales->create($staff, [
            ['product_id' => $id('DN-003'), 'quantity' => 2],
        ], now()->subDays(3)->setTime(15, 0));
        $sales->cancel($cancelled);
    }
}