<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PurchaseController extends Controller
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

        $purchases = Purchase::with(['supplier', 'user', 'items'])
            // Staff hanya melihat pembelian miliknya sendiri.
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where('purchase_number', 'like', "%{$q}%"))
            ->when($filters['status'] ?? null, fn ($query, $s) => $query->where('status', $s))
            ->when($filters['date_from'] ?? null, fn ($query, $d) => $query->whereDate('purchased_at', '>=', $d))
            ->when($filters['date_to'] ?? null, fn ($query, $d) => $query->whereDate('purchased_at', '<=', $d))
            ->latest('purchased_at')->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('purchases.index', compact('purchases', 'filters'));
    }

    public function create(Request $request)
    {
        // Dukungan "Restock cepat": ?product={id} menyeleksi produk di baris pertama.
        $preselectId = (int) $request->query('product', 0);

        $suppliers = Supplier::orderBy('name')->get();
        $products  = Product::orderBy('name')->get();
        $preselect = $preselectId ? $products->firstWhere('id', $preselectId) : null;

        return view('purchases.create', compact('suppliers', 'products', 'preselect'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'        => 'required|exists:suppliers,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_cost'  => 'required|numeric|min:0',
        ], [
            'supplier_id.required' => 'Pilih pemasok terlebih dahulu.',
            'items.min'            => 'Tambahkan minimal satu produk.',
            'items.*.quantity.min' => 'Jumlah produk minimal 1.',
            'items.*.unit_cost.min'=> 'Harga beli tidak boleh negatif.',
        ]);

        DB::transaction(function () use ($request) {
            $purchase = Purchase::create([
                'supplier_id'     => $request->supplier_id,
                'user_id'         => auth()->id() ?? 1,
                'purchase_number' => 'PUR-' . date('YmdHis') . '-' . rand(100, 999),
                'total'           => 0,
                'status'          => 'completed',
                'purchased_at'    => now(),
            ]);

            $grandTotal = 0;

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $unitCost = (float) $item['unit_cost'];
                $subtotal = $unitCost * $qty;

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_cost'  => $unitCost,
                    'subtotal'   => $subtotal,
                ]);

                $product->increment('stock', $qty);
                $grandTotal += $subtotal;
            }

            $purchase->update(['total' => $grandTotal]);
        });

        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil! Stok barang otomatis bertambah.');
    }

    public function show(Purchase $purchase)
    {
        // Staff tidak boleh melihat pembelian milik pengguna lain.
        if (! auth()->user()->isAdmin() && $purchase->user_id !== auth()->id()) {
            abort(403);
        }

        return view('purchases.show', [
            'purchase' => $purchase->loadMissing(['supplier', 'user', 'items.product']),
        ]);
    }

    public function cancel(Purchase $purchase)
    {
        if (! $purchase->isCompleted()) {
            return back()->with('error', 'Pembelian ini sudah dibatalkan sebelumnya.');
        }

        try {
            DB::transaction(function () use ($purchase) {
                foreach ($purchase->items()->get() as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item->product_id);

                    if ($product->stock < $item->quantity) {
                        throw new \RuntimeException(
                            "Pembatalan ditolak: stok '{$product->name}' sudah terpakai oleh transaksi penjualan."
                        );
                    }

                    $product->decrement('stock', $item->quantity);
                }

                $purchase->forceFill(['status' => 'cancelled'])->save();
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('purchases.show', $purchase)->with('success', 'Pembelian dibatalkan. Stok barang dikurangi kembali.');
    }
}
