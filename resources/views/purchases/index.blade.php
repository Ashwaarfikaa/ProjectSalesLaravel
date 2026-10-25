@extends('layouts.app')

@section('title', 'Pembelian - SummitGear')

@section('content')
<section>
    <div class="top-bar">
        <div>
            <h1 class="page-title">Daftar pembelian</h1>
            <p class="page-subtitle">Riwayat transaksi masuk barang (stock-in). Stok bertambah saat transaksi berhasil.</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ Pembelian baru</a>
    </div>
</section>

<div class="card table-card">
    <h2>Pencarian & filter</h2>
    <form method="GET" action="{{ route('purchases.index') }}" class="filters">
        <div class="form-group">
            <label for="q">No. transaksi</label>
            <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="PUR-...">
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
            <a href="{{ route('purchases.index') }}" class="btn">Reset</a>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>No. transaksi</th><th>Pemasok</th><th>Dicatat oleh</th><th class="num">Item</th><th class="num">Total</th><th>Waktu</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($purchases as $purchase)
                    <tr>
                        <td><a href="{{ route('purchases.show', $purchase) }}" class="mono">{{ $purchase->purchase_number }}</a></td>
                        <td>{{ $purchase->supplier->name }}</td>
                        <td>{{ $purchase->user->name }}</td>
                        <td class="num">{{ $purchase->items->sum('quantity') }}</td>
                        <td class="num">@rupiah($purchase->total)</td>
                        <td>{{ $purchase->purchased_at->format('d/m/Y H:i') }}</td>
                        <td>@include('partials.status', ['status' => $purchase->status])</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada pembelian. Mulai dari tombol "Pembelian baru".</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $purchases->links() }}
</div>
@endsection
