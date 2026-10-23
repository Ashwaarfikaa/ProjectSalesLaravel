<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dijaga middleware can:manage-purchases
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Pilih pemasok.',
            'supplier_id.exists' => 'Pemasok yang dipilih tidak tersedia.',
            'items.required' => 'Tambahkan minimal satu item pembelian.',
            'items.min' => 'Tambahkan minimal satu item pembelian.',
            'items.*.product_id.required' => 'Pilih produk pada setiap baris.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak tersedia.',
            'items.*.quantity.required' => 'Jumlah wajib diisi pada setiap baris.',
            'items.*.quantity.integer' => 'Jumlah harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Jumlah minimal 1.',
            'items.*.unit_cost.required' => 'Harga beli wajib diisi pada setiap baris.',
            'items.*.unit_cost.numeric' => 'Harga beli harus berupa angka.',
            'items.*.unit_cost.min' => 'Harga beli tidak boleh negatif.',
        ];
    }
}