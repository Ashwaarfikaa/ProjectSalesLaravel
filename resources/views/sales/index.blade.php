@extends('layouts.app')

@section('title', 'Penjualan - SummitGear')

@section('content')
<section>
    <div class="top-bar">
        <div>
            <h1 class="page-title">Daftar penjualan</h1>
            <p class="page-subtitle">
                {{ auth()->user()->isAdmin() ? 'Riwayat seluruh transaksi keluar barang (stock-out).' : 'Riwayat transaksi penjualan milik Anda sendiri.' }}
            </p>
        </div>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">+ Penjualan baru</a>
    </div>
</section>

<div class="card table-card">
    <h2>Pencarian & filter</h2>
    <form method="GET" action="{{ route('sales.index') }}" class="filters">
        <div class="form-group">
            <label for="q">No. transaksi</label>
            <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="SALE-...">
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="">Semua status</option>
                <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="form-group">
            <label for="date_from">Dari tanggal</label>
            <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
        </div>
        <div class="form-group">
            <label for="date_to">Sampai tanggal</label>
            <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
        </div>
        <div class="btn-row">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('sales.index') }}" class="btn">Reset</a>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>No. transaksi</th><th>Kasir</th><th>Pelanggan</th><th class="num">Item</th><th class="num">Total</th><th>Waktu</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td><a href="{{ route('sales.show', $sale) }}" class="mono">{{ $sale->sale_number }}</a></td>
                        <td>{{ $sale->user->name }}</td>
                        <td>{{ $sale->customer->name ?? 'Pembeli umum' }}</td>
                        <td class="num">{{ $sale->items->sum('quantity') }}</td>
                        <td class="num">@rupiah($sale->total)</td>
                        <td>{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                        <td>@include('partials.status', ['status' => $sale->status])</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada penjualan. Mulai dari tombol "Penjualan baru".</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $sales->links() }}
</div>
@endsection
