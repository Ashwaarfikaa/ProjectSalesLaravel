<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::with('category')
            ->where('stock', '>', 0) // Hanya tampilkan produk yang stoknya ready
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->orderBy('name')
            ->get();

        return view('menu.index', compact('categories', 'products'));
    }
}