<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $suppliers = Supplier::query()
            ->when($filters['q'] ?? null, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->withCount('purchases')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'filters'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());

        Supplier::create($validated);
        return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil ditambahkan!');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate($this->rules(), $this->messages());

        $supplier->update($validated);
        return redirect()->route('suppliers.index')->with('success', 'Data pemasok berhasil diperbarui!');
    }

    public function destroy(Supplier $supplier)
    {
        // Pemasok yang pernah dipakai dalam pembelian tidak boleh dihapus
        // agar riwayat transaksi tetap utuh.
        if ($supplier->purchases()->exists()) {
            return back()->with('error', 'Pemasok sudah memiliki riwayat pembelian dan tidak dapat dihapus.');
        }

        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil dihapus!');
    }

    private function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:150'],
            'phone'   => ['required', 'string', 'max:30'],
            'email'   => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function messages(): array
    {
        return [
            'name.required'  => 'Nama pemasok wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.max'      => 'Nomor telepon maksimal 30 karakter.',
            'email.email'    => 'Format email tidak valid.',
            'address.max'    => 'Alamat maksimal 500 karakter.',
        ];
    }
}
