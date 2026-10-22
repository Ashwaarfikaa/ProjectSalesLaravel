@extends('layouts.app')
@section('title', 'Penjualan Baru - Sweet Glaze')

@section('content')
@php
    $payload = [
        'mode' => 'sale',
        'products' => $products->map(fn ($p) => [
            'id' => $p->id, 'sku' => $p->sku, 'name' => $p->name,
            'price' => (float) $p->selling_price, 'stock' => $p->stock,
        ])->values(),
        'old' => array_values(old('items', [])),
    ];
@endphp

<div class="top-bar">
    <div>
        <h1 class="page-title">Penjualan baru</h1>
        <p class="page-subtitle">Pilih produk dan jumlah. Stok berkurang setelah transaksi tersimpan.</p>
    </div>
</div>

<form method="POST" action="{{ route('sales.store') }}">
    @csrf
    <div class="card" style="margin-bottom:18px">
        <div class="form-group" style="max-width:420px;margin-bottom:0">
            <label for="customer_id">Pelanggan (opsional)</label>
            <select id="customer_id" name="customer_id">
                <option value="">Pembeli umum</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}{{ $customer->phone ? ' - ' . $customer->phone : '' }}
                    </option>
                @endforeach
            </select>
            @error('customer_id') <div class="field-error">{{ $message }}</div> @enderror
            @can('manage-customers')
                <p class="hint">Pelanggan belum terdaftar? <a href="{{ route('customers.create') }}" target="_blank" rel="noopener">Tambah pelanggan baru</a>, lalu muat ulang halaman ini.</p>
            @endcan
        </div>
    </div>

    <div class="card" style="margin-bottom:18px">
        <div class="table-wrap">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="num">Harga</th>
                        <th class="num">Stok</th>
                        <th style="width:110px">Jumlah</th>
                        <th class="num">Subtotal</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="rows"></tbody>
            </table>
        </div>

        <div style="margin-top:12px">
            <button type="button" class="btn" id="addRow">+ Tambah item</button>
        </div>

        <div class="total-bar">
            <span>Total</span>
            <strong id="grand">Rp 0</strong>
        </div>
        <p class="hint">Angka ini perkiraan tampilan. Server menghitung ulang total dari harga di database.</p>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan penjualan</button>
        <a href="{{ route('sales.index') }}" class="btn">Batal</a>
    </div>
</form>

<script type="application/json" id="tx-data">@json($payload)</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var txDataEl = document.getElementById('tx-data');
    if (!txDataEl) return;

    var txData = JSON.parse(txDataEl.textContent || '{}');
    var products = txData.products || [];
    var oldItems = txData.old || [];

    var rowsContainer = document.getElementById('rows');
    var addRowBtn = document.getElementById('addRow');
    var grandEl = document.getElementById('grand');

    function formatRp(val) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
    }

    function recalculateGrand() {
        var total = 0;
        var trs = rowsContainer.querySelectorAll('tr');
        trs.forEach(function (tr) {
            var sel = tr.querySelector('.prod-select');
            var qtyInp = tr.querySelector('.qty-input');
            if (sel && sel.value && qtyInp && qtyInp.value) {
                var price = parseFloat(sel.options[sel.selectedIndex].getAttribute('data-price') || 0);
                var qty = parseInt(qtyInp.value, 10) || 0;
                total += price * qty;
            }
        });
        if (grandEl) grandEl.textContent = formatRp(total);
    }

    function createRow(initialData) {
        initialData = initialData || {};
        var index = Date.now() + Math.random().toString().slice(2, 6);
        var tr = document.createElement('tr');

        var optionsHtml = '<option value="">-- Pilih produk --</option>';
        products.forEach(function (p) {
            var selected = (initialData.product_id == p.id) ? 'selected' : '';
            optionsHtml += '<option value="' + p.id + '" data-price="' + p.price + '" data-stock="' + p.stock + '" ' + selected + '>' + p.name + ' (' + p.sku + ')</option>';
        });

        tr.innerHTML = 
            '<td><select name="items[' + index + '][product_id]" class="prod-select" required>' + optionsHtml + '</select></td>' +
            '<td class="num price-td">Rp 0</td>' +
            '<td class="num stock-td">-</td>' +
            '<td><input type="number" name="items[' + index + '][quantity]" class="qty-input" value="' + (initialData.quantity || 1) + '" min="1" required disabled></td>' +
            '<td class="num subtotal-td">Rp 0</td>' +
            '<td style="text-align:center"><button type="button" class="btn remove-row" style="padding:2px 8px; color:var(--danger, #e53e3e); border:none; background:none; font-size:18px; cursor:pointer">&times;</button></td>';

        rowsContainer.appendChild(tr);

        var sel = tr.querySelector('.prod-select');
        var qtyInp = tr.querySelector('.qty-input');
        var priceTd = tr.querySelector('.price-td');
        var stockTd = tr.querySelector('.stock-td');
        var subtotalTd = tr.querySelector('.subtotal-td');
        var removeBtn = tr.querySelector('.remove-row');

        function updateRow() {
            var opt = sel.options[sel.selectedIndex];
            if (sel.value && opt) {
                var price = parseFloat(opt.getAttribute('data-price') || 0);
                var stock = parseInt(opt.getAttribute('data-stock') || 0, 10);
                priceTd.textContent = formatRp(price);
                stockTd.textContent = stock;
                qtyInp.max = stock;
                qtyInp.disabled = false;

                var qty = parseInt(qtyInp.value, 10) || 0;
                subtotalTd.textContent = formatRp(price * qty);
            } else {
                priceTd.textContent = 'Rp 0';
                stockTd.textContent = '-';
                qtyInp.disabled = true;
                subtotalTd.textContent = 'Rp 0';
            }
            recalculateGrand();
        }

        sel.addEventListener('change', updateRow);
        qtyInp.addEventListener('input', updateRow);

        removeBtn.addEventListener('click', function () {
            tr.remove();
            recalculateGrand();
        });

        if (sel.value) {
            updateRow();
        }
    }

    if (addRowBtn) {
        addRowBtn.addEventListener('click', function () {
            createRow();
        });
    }

    if (oldItems && oldItems.length > 0) {
        oldItems.forEach(function (item) {
            createRow(item);
        });
    } else {
        createRow();
    }
});
</script>
@endpush