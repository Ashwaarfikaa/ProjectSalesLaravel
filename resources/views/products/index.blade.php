@extends('layouts.app')

@section('title', 'Produk - SummitGear')

@section('content')
<section>
    <div class="top-bar">
        <div>
            <h1 class="page-title">Daftar produk</h1>
            <p class="page-subtitle">Kelola master data produk, harga, dan stok barang.</p>
        </div>
        @can('manage-master')
            <a href="{{ route('products.create') }}" class="btn btn-primary">+ Produk baru</a>
        @endcan
    </div>
</section>

<div class="card table-card">
    <h2>Pencarian & filter</h2>
    <form method="GET" action="{{ route('products.index') }}" class="filters">
        <div class="form-group">
            <label for="q">Cari</label>
            <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="SKU atau nama produk...">
        </div>
        <div class="form-group">
            <label for="category">Kategori</label>
            <select id="category" name="category">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ ($filters['category'] ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="btn-row">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('products.index') }}" class="btn">Reset</a>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>SKU</th><th>Produk</th><th>Kategori</th>
                    <th class="num">Harga beli</th><th class="num">Harga jual</th>
                    <th class="num">Stok</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td class="mono">{{ $product->sku }}</td>
                        <td><a href="{{ route('products.show', $product) }}"><strong>{{ $product->name }}</strong></a></td>
                        <td>{{ $product->category->name }}</td>
                        <td class="num">{{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td class="num">
                            @if ($product->isLowStock())
                                <span class="badge badge-danger">{{ $product->stock }} / min {{ $product->minimum_stock }}</span>
                            @else
                                <span class="badge badge-success">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td class="actions">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm">Detail</a>
                            @can('manage-master')
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm">Ubah</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                      onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
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

    {{ $products->links() }}
</div>
@endsection
