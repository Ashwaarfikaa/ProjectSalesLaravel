<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dijaga middleware can:manage-sales
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.exists' => 'Pelanggan yang dipilih tidak tersedia.',
            'items.required' => 'Tambahkan minimal satu item penjualan.',
            'items.min' => 'Tambahkan minimal satu item penjualan.',
            'items.*.product_id.required' => 'Pilih produk pada setiap baris.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak tersedia.',
            'items.*.quantity.required' => 'Jumlah wajib diisi pada setiap baris.',
            'items.*.quantity.integer' => 'Jumlah harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Jumlah minimal 1.',
        ];
    }
}