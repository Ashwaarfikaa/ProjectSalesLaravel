@extends('layouts.app')
@section('title', $customer->name . ' - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">{{ $customer->name }}</h1>
        <p class="page-subtitle">Terdaftar sejak {{ $customer->created_at->format('d/m/Y') }}</p>
    </div>
    <div class="btn-row">
        <a href="{{ route('customers.index') }}" class="btn">Kembali</a>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-edit">Ubah pelanggan</a>
    </div>
</div>

<div class="card" style="margin-bottom:18px">
    <dl class="detail-list">
        <dt>Telepon</dt><dd>{{ $customer->phone ?: '-' }}</dd>
        <dt>Email</dt><dd>{{ $customer->email ?: '-' }}</dd>
        <dt>Alamat</dt><dd>{{ $customer->address ?: '-' }}</dd>
        <dt>Jumlah transaksi</dt><dd>{{ $salesCount }} transaksi selesai</dd>
        <dt>Total belanja</dt><dd><strong>@rupiah($totalSpent)</strong></dd>
    </dl>
</div>

<div class="card table-card">
    <h2 style="padding:18px 20px 4px">Riwayat penjualan terakhir</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. transaksi</th><th>Waktu</th><th>Kasir</th><th>Status</th><th class="num">Total</th></tr></thead>
            <tbody>
                @forelse ($recentSales as $sale)
                    <tr>
                        <td><a href="{{ route('sales.show', $sale) }}" class="mono">{{ $sale->sale_number }}</a></td>
                        <td>{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->user->name }}</td>
                        <td>@include('partials.status', ['status' => $sale->status])</td>
                        <td class="num">@rupiah($sale->total)</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada riwayat penjualan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection