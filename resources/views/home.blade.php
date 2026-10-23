@extends('layouts.app')

@section('content')
<div style="text-align: center; padding: 40px 0;">
    <h1 style="font-size: 3rem; color: #d9534f; margin-bottom: 10px;">🍩 Donuts & Drinks Inventory</h1>
    <p style="font-size: 1.2rem; color: #666; margin-bottom: 40px;">Sistem manajemen stok donat dan minuman</p>

    <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-bottom: 40px;">
        <a href="{{ route('products.index') }}" style="flex: 1; min-width: 250px; max-width: 300px; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-decoration: none; color: inherit; border: 1px solid #e9ecef;">
            <div style="font-size: 3rem; text-align: center; margin-bottom: 15px;">📦</div>
            <h3 style="text-align: center; color: #333; margin-bottom: 10px;">Kelola Produk</h3>
            <p style="text-align: center; color: #666; font-size: 0.95rem;">Lihat, tambah, edit, hapus data donat & minuman</p>
        </a>

        <div style="flex: 1; min-width: 250px; max-width: 300px; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #e9ecef;">
            <div style="font-size: 3rem; text-align: center; margin-bottom: 15px;">📊</div>
            <h3 style="text-align: center; color: #333; margin-bottom: 10px;">Stok Saat Ini</h3>
            <p style="text-align: center; color: #666; font-size: 0.95rem;">Total {{ $totalProducts }} produk tersedia</p>
        </div>
    </div>

    <div style="color: #999; font-size: 0.9rem; border-top: 1px solid #e9ecef; padding-top: 20px;">
        <p>Dibangun dengan Laravel 8 | PHP 8.0</p>
    </div>
</div>
@endsection