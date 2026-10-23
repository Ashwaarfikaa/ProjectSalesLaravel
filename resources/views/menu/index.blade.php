@extends('layouts.app')

@section('title', 'Katalog Menu - Sweet Glaze')

@section('content')
<style>
    .menu-header { text-align: center; margin-bottom: 24px; }
    .category-filter { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; }
    .category-btn { padding: 8px 16px; border-radius: 20px; text-decoration: none; border: 1px solid var(--border, #e2e8f0); color: inherit; font-size: 14px; }
    .category-btn.active { background: var(--primary, #d97706); color: #fff; border-color: var(--primary, #d97706); }
    
    .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
    .menu-card { background: #fff; border-radius: 12px; border: 1px solid var(--border, #e2e8f0); padding: 16px; display: flex; flex-direction: column; justify-content: space-between; }
    .menu-card .badge { display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 12px; background: #fef3c7; color: #92400e; font-weight: bold; margin-bottom: 8px; }
    .menu-card .title { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
    .menu-card .price { font-size: 18px; color: var(--primary, #d97706); font-weight: bold; margin-bottom: 12px; }
    .menu-card .stock { font-size: 12px; color: #64748b; margin-bottom: 12px; }
    .menu-card .btn-choose { width: 100%; padding: 8px; border-radius: 6px; border: none; background: var(--primary, #d97706); color: white; font-weight: bold; cursor: pointer; }
    .menu-card .btn-choose:hover { opacity: 0.9; }

    .cart-summary { position: fixed; bottom: 20px; right: 20px; background: #1e293b; color: #fff; padding: 16px 24px; border-radius: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 16px; z-index: 99; }
</style>

<div class="menu-header">
    <h1 class="page-title">🍩 Menu Sweet Glaze</h1>
    <p class="page-subtitle">Pilih varian donat dan minuman favoritmu</p>
</div>

<!-- Filter Kategori -->
<div class="category-filter">
    <a href="{{ route('menu.index') }}" class="category-btn {{ !request('category_id') ? 'active' : '' }}">Semua</a>
    @foreach ($categories as $cat)
        <a href="{{ route('menu.index', ['category_id' => $cat->id]) }}" 
           class="category-btn {{ request('category_id') == $cat->id ? 'active' : '' }}">
            {{ $cat->name }}
        </a>
    @endforeach
</div>

<!-- Daftar Produk -->
<div class="menu-grid">
    @forelse ($products as $p)
        <div class="menu-card">
            <div>
                <span class="badge">{{ $p->category->name }}</span>
                <div class="title">{{ $p->name }}</div>
                <div class="price">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</div>
                <div class="stock">Tersedia: {{ $p->stock }} pcs</div>
            </div>
            <button type="button" class="btn-choose" onclick="selectItem('{{ $p->name }}', {{ $p->selling_price }})">
                + Pilih
            </button>
        </div>
    @empty
        <div style="grid-column: 1/-1; text-align: center; color: #64748b; padding: 40px;">
            Belum ada produk untuk kategori ini.
        </div>
    @endforelse
</div>

<!-- Ringkasan Pilihan Pelanggan -->
<div class="cart-summary" id="cartSummary" style="display: none;">
    <div>
        <div style="font-size: 12px; opacity: 0.8;">Item dipilih: <b id="cartCount">0</b></div>
        <div style="font-size: 16px; font-weight: bold;" id="cartTotal">Rp 0</div>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary" style="background: #fff; color: #1e293b; text-decoration: none; padding: 6px 16px; border-radius: 20px; font-weight: bold;">
        Lanjut Pesan &rarr;
    </a>
</div>

<script>
    let selectedCount = 0;
    let totalPrice = 0;

    function selectItem(name, price) {
        selectedCount++;
        totalPrice += price;

        document.getElementById('cartCount').textContent = selectedCount;
        document.getElementById('cartTotal').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice);
        document.getElementById('cartSummary').style.display = 'flex';
    }
</script>
@endsection