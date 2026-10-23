<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);

        $customers = Customer::search($filters['q'] ?? null)
            ->withCount(['sales as completed_sales_count' => fn ($q) => $q->completed()])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers', 'filters'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(CustomerRequest $request)
    {
        Customer::create($request->validated());

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show(Request $request, Customer $customer)
    {
        $user = $request->user();

        return view('customers.show', [
            'customer' => $customer,
            'totalSpent' => $customer->sales()->completed()->sum('total'),
            'salesCount' => $customer->sales()->completed()->count(),
            'recentSales' => $customer->sales()->with('user')
                ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
                ->latest('sold_at')->latest('id')->limit(10)->get(),
        ]);
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('customers.show', $customer)->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        // Riwayat penjualan harus tetap utuh.
        if ($customer->sales()->exists()) {
            return back()->with('error', 'Pelanggan sudah memiliki riwayat penjualan dan tidak dapat dihapus.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}