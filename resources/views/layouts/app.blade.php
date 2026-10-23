<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sweet Glaze')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sweetglaze.css') }}">
    @stack('styles')
</head>
<body>

@auth
    @php
        $menu = [
            'Utama' => [
                ['dashboard', ['dashboard'], 'Dashboard', null,
                    '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>'],
                ['menu.index', ['menu.*'], 'Katalog Menu', null,
                    '<path d="M3 3h18v18H3z"/><path d="M21 9H3M9 21V9"/>'],
            ],
            'Master data' => [
                ['products.index', ['products.index', 'products.create', 'products.edit', 'products.show'], 'Produk', null,
                    '<path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8"/>'],
                ['products.low-stock', ['products.low-stock'], 'Stok rendah', null,
                    '<path d="M12 3l10 18H2L12 3z"/><path d="M12 10v5M12 18h.01"/>'],
                ['customers.index', ['customers.*'], 'Pelanggan', 'manage-customers',
                    '<circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3-7 7-7s7 3 7 7"/><path d="M16 4a4 4 0 010 8M22 21c0-3-2-5.5-5-6.6"/>'],
                ['categories.index', ['categories.*'], 'Kategori', 'manage-master',
                    '<path d="M20 12l-8 8-9-9V3h8l9 9z"/><circle cx="7.5" cy="7.5" r="1.5"/>'],
            ],
            'Transaksi' => [
                ['sales.index', ['sales.*'], 'Penjualan', 'manage-sales',
                    '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.5 12h11L21 7H6"/>'],
            ],
            'Administrasi' => [
                ['users.index', ['users.*'], 'Pengguna', 'manage-users',
                    '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>'],
            ],
        ];
    @endphp

    <div class="app-shell">
        <aside class="sidebar" id="sidebar" aria-label="Menu utama">
            <a href="{{ route('dashboard') }}" class="brand">
                <span class="brand-mark" aria-hidden="true">🍩</span>
                <span class="brand-name">Sweet Glaze<small>Inventory &amp; Sales</small></span>
            </a>

            <nav class="nav">
                @foreach ($menu as $group => $items)
                    @php $visible = collect($items)->filter(fn ($i) => ! $i[3] || auth()->user()->can($i[3])); @endphp
                    @if ($visible->isNotEmpty())
                        <div class="nav-label">{{ $group }}</div>
                        @foreach ($visible as $item)
                            <a href="{{ route($item[0]) }}" class="{{ request()->routeIs(...$item[1]) ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item[4] !!}</svg>
                                {{ $item[2] }}
                            </a>
                        @endforeach
                    @endif
                @endforeach
            </nav>

            <div class="sidebar-foot">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ Auth::user()->roleLabel() }}</div>
                <a href="{{ route('logout') }}" class="logout-btn" style="display:block; text-align:center; text-decoration:none;">Keluar</a>
            </div>
        </aside>

        <div class="scrim" id="scrim"></div>

        <div class="main">
            <header class="mobile-bar">
                <button type="button" class="menu-toggle" id="menuToggle" aria-label="Buka menu">☰</button>
                <strong>🍩 Sweet Glaze</strong>
            </header>

            <main class="content">
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
            </main>
        </div>
    </div>
@else
    <main class="guest-wrap">
        @yield('content')
    </main>
@endauth

<script>
    (function () {
        var toggle = document.getElementById('menuToggle');
        var scrim = document.getElementById('scrim');
        if (!toggle) return;
        toggle.addEventListener('click', function () { document.body.classList.toggle('nav-open'); });
        scrim.addEventListener('click', function () { document.body.classList.remove('nav-open'); });
    })();
</script>
@stack('scripts')
</body>
</html>