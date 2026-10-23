@extends('layouts.app')

@section('title', 'Pelanggan - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Pelanggan</h1>
        <p class="page-subtitle">Daftar pelanggan terdaftar untuk transaksi penjualan.</p>
    </div>
    @can('manage-customers')
        <a href="{{ route('customers.create') }}" class="btn btn-primary">+ Tambah Pelanggan</a>
    @endcan
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td><strong>{{ $customer->name }}</strong></td>
                        <td>{{ $customer->phone ?? '-' }}</td>
                        <td>{{ $customer->email ?? '-' }}</td>
                        <td>{{ $customer->address ?? '-' }}</td>
                        <td>
                            @can('manage-customers')
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748b; padding: 20px;">
                            Belum ada data pelanggan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($customers, 'hasPages') && $customers->hasPages())
        <div style="padding: 16px;">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection