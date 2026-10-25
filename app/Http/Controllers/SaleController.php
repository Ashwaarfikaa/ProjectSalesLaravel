<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q'         => ['nullable', 'string', 'max:60'],
            'status'    => ['nullable', 'in:completed,cancelled'],
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $user = auth()->user();

        $sales = Sale::with(['user', 'items', 'customer'])
            // Staff hanya melihat penjualan miliknya sendiri.
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where('sale_number', 'like', "%{$q}%"))
            ->when($filters['status'] ?? null, fn ($query, $s) => $query->where('status', $s))
            ->when($filters['date_from'] ?? null, fn ($query, $d) => $query->whereDate('sold_at', '>=', $d))
            ->when($filters['date_to'] ?? null, fn ($query, $d) => $query->whereDate('sold_at', '<=', $d))
            ->latest('sold_at')->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales', 'filters'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        return view('sales.create', compact('products', 'customers'));
    }

    public function show(Sale $sale)
    {
        // Staff tidak boleh melihat penjualan milik pengguna lain.
        if (! auth()->user()->isAdmin() && $sale->user_id !== auth()->id()) {
            abort(403);
        }

        return view('sales.show', [
            'sale' => $sale->loadMissing(['user', 'items.product', 'customer']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'        => 'nullable|exists:customers,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ], [
            'items.min'             => 'Tambahkan minimal satu produk.',
            'items.*.quantity.min'  => 'Jumlah produk minimal 1.',
            'customer_id.exists'    => 'Pelanggan tidak tersedia.',
        ]);

        DB::transaction(function () use ($request) {
            $sale = Sale::create([
                'user_id'     => auth()->id() ?? 1,
                'customer_id' => $request->customer_id,
                'sale_number' => 'SALE-' . date('YmdHis') . '-' . rand(100, 999),
                'total'       => 0,
                'status'      => 'completed',
                'sold_at'     => now(),
            ]);

            $grandTotal = 0;

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];

                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok '{$product->name}' tidak mencukupi (Tersedia: {$product->stock}, Diminta: {$qty}).",
                    ]);
                }

                $subtotal = $product->selling_price * $qty;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_price' => $product->selling_price,
                    'subtotal'   => $subtotal,
                ]);

                $product->decrement('stock', $qty);
                $grandTotal += $subtotal;
            }

            $sale->update(['total' => $grandTotal]);
        });

        return redirect()->route('sales.index')->with('success', 'Transaksi penjualan berhasil disimpan! Stok produk otomatis berkurang.');
    }

    public function cancel(Sale $sale)
    {
        if (! $sale->isCompleted()) {
            return back()->with('error', 'Penjualan ini sudah dibatalkan sebelumnya.');
        }

        DB::transaction(function () use ($sale) {
            foreach ($sale->items()->get() as $item) {
                Product::lockForUpdate()->findOrFail($item->product_id)->increment('stock', $item->quantity);
            }

            $sale->forceFill(['status' => 'cancelled'])->save();
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Penjualan dibatalkan. Stok produk dipulihkan.');
    }
}
