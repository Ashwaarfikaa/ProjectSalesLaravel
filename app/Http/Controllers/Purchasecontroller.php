<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:50'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'status' => ['nullable', Rule::in([Purchase::STATUS_COMPLETED, Purchase::STATUS_CANCELLED])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ], [
            'date_from.date' => 'Tanggal awal tidak valid.',
            'date_to.date' => 'Tanggal akhir tidak valid.',
            'date_to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        $user = $request->user();

        $purchases = Purchase::with(['supplier', 'user'])
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->filtered($filters)
            ->latest('purchased_at')->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('purchases.index', [
            'purchases' => $purchases,
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return view('purchases.create', [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'products' => Product::orderBy('name')
                ->get(['id', 'sku', 'name', 'purchase_price', 'stock']),
        ]);
    }

    public function store(StorePurchaseRequest $request, PurchaseService $service)
    {
        $data = $request->validated();

        $purchase = $service->create($request->user(), (int) $data['supplier_id'], $data['items']);

        return redirect()->route('purchases.show', $purchase)
            ->with('success', "Pembelian {$purchase->purchase_number} berhasil disimpan dan stok bertambah.");
    }

    public function show(Request $request, Purchase $purchase)
    {
        abort_unless($request->user()->isAdmin() || $purchase->user_id === $request->user()->id, 403);

        $purchase->load('items.product', 'supplier', 'user');

        return view('purchases.show', compact('purchase'));
    }

    public function cancel(Purchase $purchase, PurchaseService $service)
    {
        Gate::authorize('cancel-transaction');

        $service->cancel($purchase);

        return back()->with('success', 'Pembelian dibatalkan dan stok disesuaikan.');
    }
}