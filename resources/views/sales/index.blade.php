@extends('layouts.app')
@section('title', 'Penjualan - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Penjualan</h1>
        <p class="page-subtitle">{{ auth()->user()->isAdmin() ? 'Seluruh transaksi penjualan.' : 'Transaksi penjualan yang Anda catat.' }}</p>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary">Penjualan baru</a>
</div>

<form method="GET" action="{{ route('sales.index') }}" class="card filters">
    <div class="field grow">
        <label for="q">Nomor transaksi</label>
        <input type="search" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Contoh: INV-20261023">
    </div>
    <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="">Semua status</option>
            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
    </div>
    <div class="field">
        <label for="customer_id">Pelanggan</label>
        <select id="customer_id" name="customer_id">
            <option value="">Semua pelanggan</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" {{ ($filters['customer_id'] ?? '') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label for="date_from">Dari tanggal</label>
        <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
    </div>
    <div class="field">
        <label for="date_to">Sampai tanggal</label>
        <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
    </div>
    <div class="btn-row">
        <button type="submit" class="btn btn-primary">Terapkan</button>
        <a href="{{ route('sales.index') }}" class="btn">Atur ulang</a>
    </div>
</form>

<div class="card table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. transaksi</th><th>Waktu</th><th>Pelanggan</th><th>Kasir</th><th>Status</th><th class="num">Total</th><th></th></tr></thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td class="mono">{{ $sale->sale_number }}</td>
                        <td>{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->customer ? $sale->customer->name : 'Umum' }}</td>
                        <td>{{ $sale->user->name }}</td>
                        <td>@include('partials.status', ['status' => $sale->status])</td>
                        <td class="num">@rupiah($sale->total)</td>
                        <td class="actions"><a href="{{ route('sales.show', $sale) }}" class="btn btn-sm">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Tidak ada transaksi yang cocok dengan filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $sales->links() }}
@endsection