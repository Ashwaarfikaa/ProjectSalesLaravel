@php $editing = isset($product); @endphp
<div class="form-grid">
    <div class="form-group">
        <label for="sku">SKU</label>
        <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku ?? '') }}" required>
        @error('sku') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label for="category_id">Kategori</label>
        <select id="category_id" name="category_id" required>
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id ?? '') == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group full">
        <label for="name">Nama produk</label>
        <input type="text" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required>
        @error('name') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label for="purchase_price">Harga beli (Rp)</label>
        <input type="number" id="purchase_price" name="purchase_price" min="0" step="0.01" value="{{ old('purchase_price', $product->purchase_price ?? '') }}" required>
        @error('purchase_price') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label for="selling_price">Harga jual (Rp)</label>
        <input type="number" id="selling_price" name="selling_price" min="0" step="0.01" value="{{ old('selling_price', $product->selling_price ?? '') }}" required>
        @error('selling_price') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label for="stock">{{ $editing ? 'Stok saat ini' : 'Stok awal' }}</label>
        @if ($editing)
            <input type="number" id="stock" value="{{ $product->stock }}" readonly>
            <p class="hint">Stok berubah otomatis lewat transaksi pembelian dan penjualan.</p>
        @else
            <input type="number" id="stock" name="stock" min="0" step="1" value="{{ old('stock', 0) }}" required>
            @error('stock') <div class="field-error">{{ $message }}</div> @enderror
        @endif
    </div>

    <div class="form-group">
        <label for="minimum_stock">Stok minimum</label>
        <input type="number" id="minimum_stock" name="minimum_stock" min="0" step="1" value="{{ old('minimum_stock', $product->minimum_stock ?? 0) }}" required>
        @error('minimum_stock') <div class="field-error">{{ $message }}</div> @enderror
        <p class="hint">Produk masuk daftar stok rendah bila stok sama atau di bawah angka ini.</p>
    </div>
</div>