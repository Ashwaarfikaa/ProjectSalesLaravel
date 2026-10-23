@extends('layouts.app')
@section('title', 'Pemasok - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Pemasok</h1>
        <p class="page-subtitle">Data pemasok dan kontak dasar.</p>
    </div>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Tambah pemasok</a>
</div>

<form method="GET" action="{{ route('suppliers.index') }}" class="card filters">
    <div class="field grow">
        <label for="q">Cari nama atau email</label>
        <input type="search" id="q" name="q" value="{{ $filters['q'] ?? '' }}">
    </div>
    <div class="btn-row">
        <button type="submit" class="btn btn-primary">Terapkan</button>
        <a href="{{ route('suppliers.index') }}" class="btn">Atur ulang</a>
    </div>
</form>

<div class="card table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Telepon</th><th>Email</th><th class="num">Pembelian</th><th></th></tr></thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td><strong>{{ $supplier->name }}</strong></td>
                        <td>{{ $supplier->phone ?: '-' }}</td>
                        <td>{{ $supplier->email ?: '-' }}</td>
                        <td class="num">{{ $supplier->purchases_count }}</td>
                        <td class="actions">
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-edit btn-sm">Ubah</a>
                            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Hapus pemasok ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada pemasok yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $suppliers->links() }}
@endsection