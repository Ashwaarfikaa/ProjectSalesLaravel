@extends('layouts.app')

@section('title', 'Penjualan Baru - SummitGear')

@section('content')
<section>
    <div class="top-bar">
        <div>
            <h1 class="page-title">Transaksi penjualan (stock-out)</h1>
            <p class="page-subtitle">Stok dijaga server: transaksi ditolak bila jumlah melebihi stok tersedia.</p>
        </div>
        <a href="{{ route('sales.index') }}" class="btn">Kembali</a>
    </div>
</section>

<form method="POST" action="{{ route('sales.store') }}" class="card form-card" id="saleForm">
    @csrf

    <div class="form-grid">
        <div class="form-group full">
            <label for="customer_id">Pelanggan (opsional)</label>
            <select id="customer_id" name="customer_id">
                <option value="">Pembeli umum</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
            @error('customer_id') <div class="field-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <h2 style="margin-top:14px">Item penjualan</h2>
    <div class="table-wrap">
        <table class="items-table" id="itemsTable">
            <thead>
                <tr><th style="width:45%">Produk</th><th style="width:18%">Jumlah</th><th style="width:25%">Stok tersedia</th><th style="width:12%"></th></tr>
            </thead>
            <tbody>
                @php $rows = old('items', [null]); @endphp
                @foreach ($rows as $i => $item)
                    <tr>
                        <td>
                            <select name="items[{{ $i }}][product_id]" required>
                                <option value="">-- Pilih produk --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        data-stock="{{ $product->stock }}"
                                        {{ ($item['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[{{ $i }}][quantity]" min="1" value="{{ $item['quantity'] ?? 1 }}" required></td>
                        <td class="stock-cell num muted">pilih produk</td>
                        <td><button type="button" class="remove-row btn btn-sm btn-danger">&times;</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @error('items') <div class="field-error">{{ $message }}</div> @enderror

    <div class="total-bar">
        <span>Harga jual & total dihitung oleh server dari data produk.</span>
        <div class="btn-row">
            <button type="button" class="btn" id="addRow">+ Tambah item</button>
            <button type="submit" class="btn btn-primary">Simpan penjualan</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    (function () {
        let index = {{ count($rows) }};
        const tbody = document.querySelector('#itemsTable tbody');

        const stockInfo = @json($products->mapWithKeys(fn ($p) => [$p->id => ['stock' => $p->stock, 'price' => $p->selling_price]]));

        function fillStockCell(row) {
            const select = row.querySelector('select');
            const cell = row.querySelector('.stock-cell');
            const info = stockInfo[select.value];
            cell.textContent = info ? info.stock + ' pcs tersedia' : 'pilih produk';
        }

        document.getElementById('addRow').addEventListener('click', function () {
            const template = tbody.rows[0];
            const row = template.cloneNode(true);
            const i = index;
            row.querySelector('select').name = `items[${i}][product_id]`;
            row.querySelector('select').value = '';
            row.querySelector('input').name = `items[${i}][quantity]`;
            row.querySelector('input').value = 1;
            tbody.appendChild(row);
            index++;
            fillStockCell(row);
        });

        tbody.addEventListener('change', function (e) {
            if (e.target.tagName === 'SELECT') fillStockCell(e.target.closest('tr'));
        });

        tbody.querySelectorAll('tr').forEach(fillStockCell);
    })();
</script>
@endpush
