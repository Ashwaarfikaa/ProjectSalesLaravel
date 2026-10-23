<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['customer', 'product'])->latest()->get();
        $products = Product::where('stock', '>', 0)->get();
        $customers = Customer::all();

        return view('transactions.index', compact('transactions', 'products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi!');
        }

        $totalPrice = $product->price * $request->quantity;

        // Simpan Transaksi
        Transaction::create([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $totalPrice,
        ]);

        // Potong Stok Otomatis
        $product->decrement('stock', $request->quantity);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dan stok terpotong!');
    }
}