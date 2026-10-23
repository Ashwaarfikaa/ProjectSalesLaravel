@extends('layouts.app')
@section('title', 'Produk - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Produk</h1>
        <p class="page-subtitle">Daftar donat dan minuman beserta stoknya.</p>
    </div>
    <div class="btn-row">
        <a href="{{ route('products.low-stock') }}" class="btn">Stok rendah</a>
        @can('manage-master')
            <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah produk</a>
        @endcan
    </div>
</div>

<form method="GET" action="{{ route('products.index') }}" class="card filters">
    <div class="field grow">
        <label for="q">Cari SKU atau nama</label>
        <input type="search" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Contoh: DN-001 atau Glaze">
    </div>
    <div class="field">
        <label for="category_id">Kategori</label>
        <select id="category_id" name="category_id">
            <option value="">Semua kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ (($filters['category_id'] ?? '') == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="btn-row">
        <button type="submit" class="btn btn-primary">Terapkan</button>
        <a href="{{ route('products.index') }}" class="btn">Atur ulang</a>
    </div>
</form>

<div class="card table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>SKU</th><th>Nama</th><th>Kategori</th>
                    <th class="num">Harga beli</th><th class="num">Harga jual</th><th class="num">Stok</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="{{ $product->isLowStock() ? 'is-low' : '' }}">
                        <td class="mono">{{ $product->sku }}</td>
                        <td><a href="{{ route('products.show', $product) }}"><strong>{{ $product->name }}</strong></a></td>
                        <td>{{ $product->category->name }}</td>
                        <td class="num">@rupiah($product->purchase_price)</td>
                        <td class="num">@rupiah($product->selling_price)</td>
                        <td class="num">
                            {{ $product->stock }}
                            @if ($product->isLowStock()) <span class="badge badge-danger">Rendah</span> @endif
                        </td>
                        <td class="actions">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm">Detail</a>
                            @can('manage-master')
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-edit btn-sm">Ubah</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Tidak ada produk yang cocok dengan pencarian.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $products->links() }}
@endsection