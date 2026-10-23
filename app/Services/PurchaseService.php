<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    /**
     * Simpan pembelian (stock-in) secara atomik dan tambahkan stok.
     *
     * @param  array<int, array{product_id:int|string, quantity:int|string, unit_cost:float|string}>  $items
     */
    public function create(User $user, int $supplierId, array $items, ?Carbon $purchasedAt = null): Purchase
    {
        $lines = collect($items)
            ->map(fn ($i) => [
                'product_id' => (int) $i['product_id'],
                'quantity' => (int) $i['quantity'],
                'unit_cost' => (float) $i['unit_cost'],
            ])
            ->sortBy('product_id')
            ->values();

        return DB::transaction(function () use ($user, $supplierId, $lines, $purchasedAt) {
            $purchase = Purchase::create([
                'supplier_id' => $supplierId,
                'user_id' => $user->id,
                'purchase_number' => 'TMP-' . uniqid('', true),
                'total' => 0,
                'status' => Purchase::STATUS_COMPLETED,
                'purchased_at' => $purchasedAt ?? now(),
            ]);

            $purchase->update([
                'purchase_number' => sprintf('PO-%s-%05d', $purchase->purchased_at->format('Ymd'), $purchase->id),
            ]);

            $total = 0;

            foreach ($lines as $line) {
                if ($line['quantity'] < 1 || $line['unit_cost'] < 0) {
                    throw ValidationException::withMessages([
                        'items' => 'Jumlah harus lebih dari 0 dan harga tidak boleh negatif.',
                    ]);
                }

                $product = Product::lockForUpdate()->find($line['product_id']);

                if (! $product) {
                    throw ValidationException::withMessages(['items' => 'Produk tidak ditemukan.']);
                }

                $subtotal = round($line['unit_cost'] * $line['quantity'], 2);

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $line['quantity'],
                    'unit_cost' => $line['unit_cost'],
                    'subtotal' => $subtotal,
                ]);

                $product->increment('stock', $line['quantity']);
                $total += $subtotal;
            }

            $purchase->update(['total' => $total]);

            return $purchase;
        });
    }

    /**
     * Batalkan pembelian. Ditolak jika stok saat ini sudah tidak cukup
     * untuk dikurangi (stok tidak boleh negatif).
     */
    public function cancel(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $purchase = Purchase::with('items')->lockForUpdate()->findOrFail($purchase->id);

            if (! $purchase->isCompleted()) {
                throw ValidationException::withMessages(['status' => 'Transaksi ini sudah dibatalkan.']);
            }

            foreach ($purchase->items->sortBy('product_id') as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                if ($product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'status' => "Pembelian tidak dapat dibatalkan: stok {$product->name} saat ini {$product->stock}, kurang dari {$item->quantity} yang masuk.",
                    ]);
                }

                $product->decrement('stock', $item->quantity);
            }

            $purchase->update(['status' => Purchase::STATUS_CANCELLED]);
        });
    }
}