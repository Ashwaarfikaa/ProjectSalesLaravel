<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SummitGear')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/summitgear.css') }}">
    @stack('styles')
</head>
<body>

@auth
    @php
        // Navigasi utama horizontal (topbar) — tampil untuk admin & staff
        $primary = [
            ['dashboard', ['dashboard'], 'Dashboard', null,
                '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>'],
            ['products.index', ['products.index', 'products.create', 'products.edit', 'products.show'], 'Produk', null,
                '<path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8"/>'],
            ['products.low-stock', ['products.low-stock'], 'Stok Rendah', null,
                '<path d="M12 3l10 18H2L12 3z"/><path d="M12 10v5M12 18h.01"/>'],
            ['sales.index', ['sales.index', 'sales.create', 'sales.show'], 'Penjualan', 'manage-sales',
                '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.5 12h11L21 7H6"/>'],
            ['purchases.index', ['purchases.index', 'purchases.create', 'purchases.show'], 'Pembelian', null,
                '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><path d="M3 6h18M16 10a4 4 0 01-8 0"/>'],
            ['customers.index', ['customers.*'], 'Pelanggan', 'manage-customers',
                '<circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3-7 7-7s7 3 7 7"/><path d="M16 4a4 4 0 010 8M22 21c0-3-2-5.5-5-6.6"/>'],
        ];

        // Dropdown "Master": modul administrasi (gated sesuai role)
        $master = [
            ['suppliers.index', ['suppliers.index', 'suppliers.create', 'suppliers.edit'], 'Pemasok', 'manage-master',
                '<path d="M3 7h11v10H3z"/><path d="M14 10h4l3 3v4h-7"/><circle cx="7" cy="19" r="1.6"/><circle cx="17" cy="19" r="1.6"/>'],
            ['categories.index', ['categories.*'], 'Kategori', 'manage-master',
                '<path d="M20 12l-8 8-9-9V3h8l9 9z"/><circle cx="7.5" cy="7.5" r="1.5"/>'],
            ['users.index', ['users.*'], 'Pengguna', 'manage-users',
                '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>'],
        ];

        $primaryItems = collect($primary)->filter(fn ($i) => ! $i[3] || auth()->user()->can($i[3]));
        $masterItems  = collect($master)->filter(fn ($i) => ! $i[3] || auth()->user()->can($i[3]));
        $masterActive = $masterItems->contains(fn ($i) => request()->routeIs(...$i[1]));
    @endphp

    <header class="topbar">
        <div class="topbar-inner">
            <a href="{{ route('dashboard') }}" class="brand">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20L10 7l4 6 3-4 4 11H3z"/></svg>
                </span>
                <span class="brand-name">SummitGear<small>Outdoor &middot; Inventory</small></span>
            </a>

            <nav class="nav" aria-label="Menu utama">
                @foreach ($primaryItems as $item)
                    <a href="{{ route($item[0]) }}" class="{{ request()->routeIs(...$item[1]) ? 'active' : '' }}">{{ $item[2] }}</a>
                @endforeach

                @if ($masterItems->isNotEmpty())
                    <div class="nav-drop {{ $masterActive ? 'has-active' : '' }}">
                        <button type="button" class="nav-drop-btn {{ $masterActive ? 'active' : '' }}" aria-expanded="false">
                            Master
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div class="drop-menu" role="menu">
                            @foreach ($masterItems as $item)
                                <a href="{{ route($item[0]) }}" role="menuitem" class="{{ request()->routeIs(...$item[1]) ? 'active' : '' }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item[4] !!}</svg>
                                    {{ $item[2] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </nav>

            <div class="topbar-actions">
                <div class="user-chip" title="{{ Auth::user()->roleLabel() }}">
                    <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    <span class="user-meta">
                        <strong>{{ Auth::user()->name }}</strong>
                        <small>{{ Auth::user()->roleLabel() }}</small>
                    </span>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="btn-ghost">Keluar</button>
                </form>

                <button type="button" class="menu-toggle" id="menuToggle" aria-label="Buka menu" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>
        </div>
    </header>

    <div class="scrim" id="scrim"></div>

    <nav class="drawer" id="drawer" aria-label="Menu seluler">
        <div class="drawer-head">SummitGear &middot; Navigasi</div>

        <div class="drawer-group">
            @foreach ($primaryItems as $item)
                <a href="{{ route($item[0]) }}" class="{{ request()->routeIs(...$item[1]) ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item[4] !!}</svg>
                    {{ $item[2] }}
                </a>
            @endforeach
        </div>

        @if ($masterItems->isNotEmpty())
            <div class="drawer-group">
                <div class="drawer-label">Master &amp; administrasi</div>
                @foreach ($masterItems as $item)
                    <a href="{{ route($item[0]) }}" class="{{ request()->routeIs(...$item[1]) ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item[4] !!}</svg>
                        {{ $item[2] }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="drawer-foot">
            <div class="user-name">{{ Auth::user()->name }}</div>
            <div class="user-role">{{ Auth::user()->roleLabel() }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Keluar</button>
            </form>
        </div>
    </nav>

    <main class="page">
        <div class="page-inner">
            @if (session('success'))
                <div class="alert alert-success" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        (function () {
            var drop = document.querySelector('.nav-drop');
            var dropBtn = drop ? drop.querySelector('.nav-drop-btn') : null;
            var toggle = document.getElementById('menuToggle');
            var scrim = document.getElementById('scrim');

            if (dropBtn) {
                dropBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var open = drop.classList.toggle('open');
                    dropBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
                document.addEventListener('click', function (e) {
                    if (!drop.contains(e.target)) drop.classList.remove('open');
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') drop.classList.remove('open');
                });
            }

            if (toggle) {
                toggle.addEventListener('click', function () {
                    var open = document.body.classList.toggle('nav-open');
                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
                scrim.addEventListener('click', function () {
                    document.body.classList.remove('nav-open');
                });
            }
        })();
    </script>
@else
    <main class="guest-wrap">
        @yield('content')
    </main>
@endauth

@stack('scripts')
</body>
</html>
