@extends('layouts.app')
@section('title', 'Pembelian Baru - Sweet Glaze')

@section('content')
@php
    $payload = [
        'mode' => 'purchase',
        'products' => $products->map(fn ($p) => [
            'id' => $p->id, 'sku' => $p->sku, 'name' => $p->name,
            'price' => (float) $p->purchase_price, 'stock' => $p->stock,
        ])->values(),
        'old' => array_values(old('items', [])),
    ];
@endphp

<div class="top-bar">
    <div>
        <h1 class="page-title">Pembelian baru</h1>
        <p class="page-subtitle">Catat barang masuk dari pemasok. Stok bertambah setelah transaksi tersimpan.</p>
    </div>
</div>

<form method="POST" action="{{ route('purchases.store') }}">
    @csrf
    <div class="card form-card" style="margin-bottom:18px; max-width:none">
        <div class="form-group" style="max-width:420px">
            <label for="supplier_id">Pemasok</label>
            <select id="supplier_id" name="supplier_id" required>
                <option value="">Pilih pemasok</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                @endforeach
            </select>
            @error('supplier_id') <div class="field-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="card" style="margin-bottom:18px">
        <div class="table-wrap">
            <table class="items-table">
                <thead>
                    <tr><th>Produk</th><th>Harga beli (Rp)</th><th class="num">Stok saat ini</th><th>Jumlah</th><th class="num">Subtotal</th><th></th></tr>
                </thead>
                <tbody id="rows"></tbody>
            </table>
        </div>

        <div style="margin-top:12px"><button type="button" class="btn" id="addRow">+ Tambah item</button></div>

        <div class="total-bar"><span>Total</span><strong id="grand">Rp 0</strong></div>
        <p class="hint">Server menghitung ulang subtotal dan total saat menyimpan.</p>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan pembelian</button>
        <a href="{{ route('purchases.index') }}" class="btn">Batal</a>
    </div>
</form>

<script type="application/json" id="tx-data">@json($payload)</script>
@endsection

@push('scripts')
<script src="{{ asset('js/transaction-form.js') }}"></script>
@endpush