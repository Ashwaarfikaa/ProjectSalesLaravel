@extends('layouts.app')
@section('title', 'Stok Rendah - SummitGear')

@section('content')
<div class="top-bar">
   <div>
        <h1 class="page-title">Stok rendah</h1>
        <p class="page-subtitle">Produk dengan stok kurang dari atau sama dengan batas minimum</p>
    </div>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>SKU</th><th>Nama</th><th>Kategori</th><th class="num">Stok</th><th class="num">Minimum</th><th class="num">Kekurangan</th><th class="actions">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="is-low">
                        <td class="mono">{{ $product->sku }}</td>
                        <td><a href="{{ route('products.show', $product) }}"><strong>{{ $product->name }}</strong></a></td>
                        <td>{{ $product->category->name }}</td>
                        <td class="num"><span class="badge badge-danger">{{ $product->stock }}</span></td>
                        <td class="num">{{ $product->minimum_stock }}</td>
                        <td class="num">{{ max(0, $product->minimum_stock - $product->stock) }}</td>
                        <td class="actions">
                            <a class="btn btn-sm btn-warning" href="{{ route('purchases.create', ['product' => $product->id]) }}">
                                Restock cepat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Semua stok masih di atas batas minimum.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $products->links() }}
@endsection