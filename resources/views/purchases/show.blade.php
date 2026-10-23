@extends('layouts.app')
@section('title', $purchase->purchase_number . ' - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">{{ $purchase->purchase_number }}</h1>
        <p class="page-subtitle">@include('partials.status', ['status' => $purchase->status])</p>
    </div>
    <div class="btn-row">
        <a href="{{ route('purchases.index') }}" class="btn">Kembali</a>
        @can('cancel-transaction')
            @if ($purchase->isCompleted())
                <form method="POST" action="{{ route('purchases.cancel', $purchase) }}" onsubmit="return confirm('Batalkan pembelian ini? Stok akan dikurangi kembali.')">
                    @csrf
                    <button type="submit" class="btn btn-danger">Batalkan pembelian</button>
                </form>
            @endif
        @endcan
    </div>
</div>

<div class="card" style="margin-bottom:18px">
    <dl class="detail-list">
        <dt>Waktu</dt><dd>{{ $purchase->purchased_at->format('d/m/Y H:i') }}</dd>
        <dt>Pemasok</dt><dd>{{ $purchase->supplier->name }}</dd>
        <dt>Dicatat oleh</dt><dd>{{ $purchase->user->name }}</dd>
        <dt>Total</dt><dd><strong>@rupiah($purchase->total)</strong></dd>
    </dl>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>SKU</th><th>Produk</th><th class="num">Harga beli</th><th class="num">Jumlah</th><th class="num">Subtotal</th></tr></thead>
            <tbody>
                @foreach ($purchase->items as $item)
                    <tr>
                        <td class="mono">{{ $item->product->sku }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td class="num">@rupiah($item->unit_cost)</td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">@rupiah($item->subtotal)</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="4" class="num">Total</td><td class="num">@rupiah($purchase->total)</td></tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection