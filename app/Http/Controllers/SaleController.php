<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', Rule::in([Sale::STATUS_COMPLETED, Sale::STATUS_CANCELLED])],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ], [
            'date_from.date' => 'Tanggal awal tidak valid.',
            'date_to.date' => 'Tanggal akhir tidak valid.',
            'date_to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        $user = $request->user();

        $sales = Sale::with(['user', 'customer'])
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->filtered($filters)
            ->latest('sold_at')->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')
            ->get(['id', 'sku', 'name', 'selling_price', 'stock']);

        return view('sales.create', [
            'products' => $products,
            'customers' => Customer::orderBy('name')->get(['id', 'name', 'phone']),
        ]);
    }

    public function store(StoreSaleRequest $request, SaleService $service)
    {
        $data = $request->validated();

        $sale = $service->create(
            $request->user(),
            $data['items'],
            null,
            ! empty($data['customer_id']) ? (int) $data['customer_id'] : null
        );

        return redirect()->route('sales.show', $sale)
            ->with('success', "Penjualan {$sale->sale_number} berhasil disimpan.");
    }

    public function show(Request $request, Sale $sale)
    {
        // Staff hanya boleh melihat transaksi miliknya sendiri.
        abort_unless($request->user()->isAdmin() || $sale->user_id === $request->user()->id, 403);

        $sale->load('items.product', 'user', 'customer');

        return view('sales.show', compact('sale'));
    }

    public function cancel(Sale $sale, SaleService $service)
    {
        Gate::authorize('cancel-transaction');

        $service->cancel($sale);

        return back()->with('success', 'Penjualan dibatalkan dan stok dipulihkan.');
    }
}