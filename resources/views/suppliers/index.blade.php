@extends('layouts.app')

@section('title', 'Pemasok - SummitGear')

@section('content')
<section>
    <div class="top-bar">
        <div>
            <h1 class="page-title">Daftar pemasok</h1>
            <p class="page-subtitle">Data pemasok barang untuk transaksi pembelian stok.</p>
        </div>
        @can('manage-master')
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">+ Pemasok baru</a>
        @endcan
    </div>
</section>

<div class="card table-card">
    <h2>Pencarian</h2>
    <form method="GET" action="{{ route('suppliers.index') }}" class="filters">
        <div class="form-group">
            <label for="q">Cari</label>
            <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nama, telepon, atau email...">
        </div>
        <div class="btn-row">
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="{{ route('suppliers.index') }}" class="btn">Reset</a>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Nama</th><th>Telepon</th><th>Email</th><th>Alamat</th><th class="num">Pembelian</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td><strong>{{ $supplier->name }}</strong></td>
                        <td class="mono">{{ $supplier->phone }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td>{{ $supplier->address ?? '-' }}</td>
                        <td class="num">{{ $supplier->purchases_count }}</td>
                        <td class="actions">
                            @can('manage-master')
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm">Ubah</a>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                                      onsubmit="return confirm('Hapus pemasok ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada data pemasok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $suppliers->links() }}
</div>
@endsection
