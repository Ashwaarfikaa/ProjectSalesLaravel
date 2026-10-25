@extends('layouts.app')

@section('title', 'Pembelian Baru - SummitGear')

@section('content')
<section>
    <div class="top-bar">
        <div>
            <h1 class="page-title">Transaksi pembelian (stock-in)</h1>
            <p class="page-subtitle">Stok produk bertambah otomatis setelah transaksi berhasil disimpan.</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn">Kembali</a>
    </div>
</section>

<form method="POST" action="{{ route('purchases.store') }}" class="card form-card" id="purchaseForm">
    @csrf

    <div class="form-grid">
        <div class="form-group full">
            <label for="supplier_id">Pemasok</label>
            <select id="supplier_id" name="supplier_id" required>
                <option value="">-- Pilih pemasok --</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->phone }})</option>
                @endforeach
            </select>
            @error('supplier_id') <div class="field-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <h2 style="margin-top:14px">Item pembelian</h2>
    <div class="table-wrap">
        <table class="items-table" id="itemsTable">
            <thead>
                <tr><th style="width:45%">Produk</th><th style="width:20%">Jumlah</th><th style="width:25%">Harga beli / unit</th><th style="width:10%"></th></tr>
            </thead>
            <tbody>
                @if (old('items'))
                    @foreach (old('items') as $i => $item)
                        <tr>
                            <td>
                                <select name="items[{{ $i }}][product_id]" required>
                                    <option value="">-- Pilih produk --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ ($item['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="items[{{ $i }}][quantity]" min="1" value="{{ $item['quantity'] ?? 1 }}" required></td>
                            <td><input type="number" name="items[{{ $i }}][unit_cost]" min="0" step="100" value="{{ $item['unit_cost'] ?? $products->first()?->purchase_price ?? 0 }}" required></td>
                            <td><button type="button" class="remove-row btn btn-sm btn-danger">&times;</button></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>
                            <select name="items[0][product_id]" required>
                                <option value="">-- Pilih produk --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ $preselect?->id === $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[0][quantity]" min="1" value="1" required></td>
                        <td><input type="number" name="items[0][unit_cost]" min="0" step="100" value="{{ $preselect?->purchase_price ?? 0 }}" required></td>
                        <td><button type="button" class="remove-row btn btn-sm btn-danger">&times;</button></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    @error('items') <div class="field-error">{{ $message }}</div> @enderror

    <div class="total-bar">
        <span>Harga & jumlah harus valid; total dihitung oleh server.</span>
        <div class="btn-row">
            <button type="button" class="btn" id="addRow">+ Tambah item</button>
            <button type="submit" class="btn btn-primary">Simpan pembelian</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    (function () {
        let index = {{ count(old('items', [])) || 1 }};
        const tbody = document.querySelector('#itemsTable tbody');
        const productOptions = @json($products->map(fn ($p) => ['id' => $p->id, 'label' => $p->name . ' (' . $p->sku . ')', 'cost' => $p->purchase_price]));

        document.getElementById('addRow').addEventListener('click', function () {
            const row = document.createElement('tr');
            row.innerHTML =
                '<td><select name="items[' + index + '][product_id]" required>' +
                '<option value="">-- Pilih produk --</option>' +
                productOptions.map(function (p) {
                    return '<option value="' + p.id + '">' + p.label.replace(/</g, '&lt;') + '</option>';
                }).join('') +
                '</select></td>' +
                '<td><input type="number" name="items[' + index + '][quantity]" min="1" value="1" required></td>' +
                '<td><input type="number" name="items[' + index + '][unit_cost]" min="0" step="100" value="0" required></td>' +
                '<td><button type="button" class="remove-row btn btn-sm btn-danger">&times;</button></td>';
            tbody.appendChild(row);
            index++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                if (tbody.rows.length > 1) e.target.closest('tr').remove();
            }
        });
    })();
</script>
@endpush
