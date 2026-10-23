@extends('layouts.app')
@section('title', 'Pembelian - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Pembelian stok</h1>
        <p class="page-subtitle">{{ auth()->user()->isAdmin() ? 'Seluruh transaksi stock-in.' : 'Pembelian yang Anda catat.' }}</p>
    </div>
    <a href="{{ route('purchases.create') }}" class="btn btn-primary">Pembelian baru</a>
</div>

<form method="GET" action="{{ route('purchases.index') }}" class="card filters">
    <div class="field grow">
        <label for="q">Nomor transaksi</label>
        <input type="search" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Contoh: PO-20261023">
    </div>
    <div class="field">
        <label for="supplier_id">Pemasok</label>
        <select id="supplier_id" name="supplier_id">
            <option value="">Semua pemasok</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ ($filters['supplier_id'] ?? '') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
            @endforeach
        </select>
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
        <label for="date_from">Dari tanggal</label>
        <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
    </div>
    <div class="field">
        <label for="date_to">Sampai tanggal</label>
        <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
    </div>
    <div class="btn-row">
        <button type="submit" class="btn btn-primary">Terapkan</button>
        <a href="{{ route('purchases.index') }}" class="btn">Atur ulang</a>
    </div>
</form>

<div class="card table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. transaksi</th><th>Waktu</th><th>Pemasok</th><th>Dicatat oleh</th><th>Status</th><th class="num">Total</th><th></th></tr></thead>
            <tbody>
                @forelse ($purchases as $purchase)
                    <tr>
                        <td class="mono">{{ $purchase->purchase_number }}</td>
                        <td>{{ $purchase->purchased_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $purchase->supplier->name }}</td>
                        <td>{{ $purchase->user->name }}</td>
                        <td>@include('partials.status', ['status' => $purchase->status])</td>
                        <td class="num">@rupiah($purchase->total)</td>
                        <td class="actions"><a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Tidak ada pembelian yang cocok dengan filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $purchases->links() }}
@endsection