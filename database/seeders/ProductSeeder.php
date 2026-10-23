<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'Donut Classic', 'category' => 'Donuts', 'price' => 15000, 'stock' => 50, 'image' => '1790576380_6aba06fc82a0d.jpeg'],
            ['name' => 'Donut Cokelat', 'category' => 'Donuts', 'price' => 18000, 'stock' => 40, 'image' => '1790576626_6aba07f2bc99f.jpeg'],
            ['name' => 'Donut Strawberry', 'category' => 'Donuts', 'price' => 18000, 'stock' => 35, 'image' => '1790576655_6aba080f5671b.jpeg'],
            ['name' => 'Donut Matcha', 'category' => 'Donuts', 'price' => 20000, 'stock' => 30, 'image' => '1790577728_6aba0c40863ab.jpeg'],
            ['name' => 'Donut Oreo', 'category' => 'Donuts', 'price' => 20000, 'stock' => 25, 'image' => '1790577793_6aba0c81014eb.jpeg'],
            ['name' => 'Donut Keju', 'category' => 'Donuts', 'price' => 17000, 'stock' => 45, 'image' => '1790577805_6aba0c8d7bab5.jpeg'],
            ['name' => 'Donut Tiramisu', 'category' => 'Donuts', 'price' => 22000, 'stock' => 20, 'image' => '1790577822_6aba0c9e2dfc8.jpeg'],
            ['name' => 'Donut Blueberry', 'category' => 'Donuts', 'price' => 18000, 'stock' => 30, 'image' => '1790577901_6aba0cedd4a21.jpeg'],
            ['name' => 'Espresso', 'category' => 'Drinks', 'price' => 18000, 'stock' => 60, 'image' => '1790577963_6aba0d2b005db.jpeg'],
            ['name' => 'Cappuccino', 'category' => 'Drinks', 'price' => 22000, 'stock' => 50, 'image' => '1790577985_6aba0d41c45ee.jpeg'],
            ['name' => 'Latte', 'category' => 'Drinks', 'price' => 25000, 'stock' => 45, 'image' => '1790578002_6aba0d529e546.jpeg'],
            ['name' => 'Mocha', 'category' => 'Drinks', 'price' => 25000, 'stock' => 40, 'image' => '1790578020_6aba0d64e6ff2.jpeg'],
            ['name' => 'Americano', 'category' => 'Drinks', 'price' => 20000, 'stock' => 55, 'image' => '1790578495_6aba0f3ff0ce6.jpeg'],
            ['name' => 'Chocolate Milk', 'category' => 'Drinks', 'price' => 15000, 'stock' => 50, 'image' => '1790578514_6aba0f52e2f45.jpeg'],
            ['name' => 'Strawberry Milk', 'category' => 'Drinks', 'price' => 18000, 'stock' => 40, 'image' => '1790578532_6aba0f64b6049.jpeg'],
            ['name' => 'Matcha Latte', 'category' => 'Drinks', 'price' => 25000, 'stock' => 35, 'image' => '1790578575_6aba0f8f72580.jpeg'],
            ['name' => 'Caramel Macchiato', 'category' => 'Drinks', 'price' => 28000, 'stock' => 30, 'image' => '1790578606_6aba0fae3247b.jpg'],
            ['name' => 'Vanilla Latte', 'category' => 'Drinks', 'price' => 26000, 'stock' => 35, 'image' => '1790578630_6aba0fc616b04.jpg'],
            ['name' => 'Hazelnut Latte', 'category' => 'Drinks', 'price' => 27000, 'stock' => 30, 'image' => '1790578644_6aba0fd430e13.jpg'],
            ['name' => 'Thai Tea', 'category' => 'Drinks', 'price' => 18000, 'stock' => 50, 'image' => '1790579337_6aba1289025b7.jpeg'],
            ['name' => 'Green Tea', 'category' => 'Drinks', 'price' => 15000, 'stock' => 55, 'image' => '1790579391_6aba12bfed729.jpeg'],
            ['name' => 'Lemon Tea', 'category' => 'Drinks', 'price' => 15000, 'stock' => 60, 'image' => '1790579441_6aba12f1c2383.jpeg'],
            ['name' => 'Lychee Tea', 'category' => 'Drinks', 'price' => 20000, 'stock' => 40, 'image' => '1790579475_6aba131364c70.jpeg'],
            ['name' => 'Mango Smoothie', 'category' => 'Drinks', 'price' => 25000, 'stock' => 25, 'image' => '1790579557_6aba136513c81.jpg'],
            ['name' => 'Banana Smoothie', 'category' => 'Drinks', 'price' => 22000, 'stock' => 30, 'image' => '1790579714_6aba1402695a4.jpeg'],
            ['name' => 'Avocado Smoothie', 'category' => 'Drinks', 'price' => 28000, 'stock' => 20, 'image' => '1790580275_6aba16332c656.jpeg'],
            ['name' => 'Strawberry Smoothie', 'category' => 'Drinks', 'price' => 25000, 'stock' => 25, 'image' => '1790580297_6aba1649f0f35.jpeg'],
            ['name' => 'Chocolate Smoothie', 'category' => 'Drinks', 'price' => 26000, 'stock' => 20, 'image' => '1790581588_6aba1b5441aab.jpeg'],
            ['name' => 'Cookies & Cream', 'category' => 'Drinks', 'price' => 27000, 'stock' => 18, 'image' => '1790581625_6aba1b799fbd7.jpeg'],
            ['name' => 'Taro Milk Tea', 'category' => 'Drinks', 'price' => 22000, 'stock' => 35, 'image' => '1790581668_6aba1ba4eb156.jpeg'],
            ['name' => 'Brown Sugar Milk Tea', 'category' => 'Drinks', 'price' => 24000, 'stock' => 30, 'image' => '1790581703_6aba1bc7665bb.jpeg'],
            ['name' => 'Wintermelon Tea', 'category' => 'Drinks', 'price' => 18000, 'stock' => 40, 'image' => '1790581914_6aba1c9a69c7e.jpeg'],
            ['name' => 'Jasmine Tea', 'category' => 'Drinks', 'price' => 15000, 'stock' => 50, 'image' => '1790581967_6aba1ccf63deb.jpeg'],
            ['name' => 'Donut Premium Box', 'category' => 'Donuts', 'price' => 150000, 'stock' => 10, 'image' => '1790587162_6aba311a87eb6.jpg'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}