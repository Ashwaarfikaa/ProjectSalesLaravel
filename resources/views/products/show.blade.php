@extends('layouts.app')
@section('title', $product->name . ' - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">{{ $product->name }}</h1>
        <p class="page-subtitle"><span class="mono">{{ $product->sku }}</span> &middot; {{ $product->category->name }}</p>
    </div>
    <div class="btn-row">
        <a href="{{ route('products.index') }}" class="btn">Kembali</a>
        @can('manage-master')
            <a href="{{ route('products.edit', $product) }}" class="btn btn-edit">Ubah produk</a>
        @endcan
    </div>
</div>

<div class="card" style="margin-bottom:18px">
    <dl class="detail-list">
        <dt>Harga beli</dt><dd>@rupiah($product->purchase_price)</dd>
        <dt>Harga jual</dt><dd>@rupiah($product->selling_price)</dd>
        <dt>Stok saat ini</dt>
        <dd>
            <strong>{{ $product->stock }}</strong>
            @if ($product->isLowStock()) <span class="badge badge-danger">Stok rendah</span> @else <span class="badge badge-success">Aman</span> @endif
        </dd>
        <dt>Stok minimum</dt><dd>{{ $product->minimum_stock }}</dd>
    </dl>
</div>

<div class="detail-grid">
    <div class="card table-card">
        <h2 style="padding:18px 20px 4px">Penjualan terakhir</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Transaksi</th><th class="num">Jumlah</th><th class="num">Harga</th></tr></thead>
                <tbody>
                    @forelse ($recentSales as $item)
                        <tr>
                            <td>
                                @if (auth()->user()->isAdmin() || $item->sale->user_id === auth()->id())
                                    <a class="mono" href="{{ route('sales.show', $item->sale) }}">{{ $item->sale->sale_number }}</a>
                                @else
                                    <span class="mono">{{ $item->sale->sale_number }}</span>
                                @endif
                            </td>
                            <td class="num">{{ $item->quantity }}</td>
                            <td class="num">@rupiah($item->unit_price)</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="empty">Belum pernah terjual.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card table-card">
        <h2 style="padding:18px 20px 4px">Pembelian terakhir</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Transaksi</th><th class="num">Jumlah</th><th class="num">Harga</th></tr></thead>
                <tbody>
                    @forelse ($recentPurchases as $item)
                        <tr>
                            <td>
                                @if (auth()->user()->isAdmin() || $item->purchase->user_id === auth()->id())
                                    <a class="mono" href="{{ route('purchases.show', $item->purchase) }}">{{ $item->purchase->purchase_number }}</a>
                                @else
                                    <span class="mono">{{ $item->purchase->purchase_number }}</span>
                                @endif
                            </td>
                            <td class="num">{{ $item->quantity }}</td>
                            <td class="num">@rupiah($item->unit_cost)</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="empty">Belum pernah dibeli.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection