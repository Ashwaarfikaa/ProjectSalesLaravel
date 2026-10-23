<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Glaze - Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f8f9fa; margin: 0; padding: 40px 20px; }
        .container { max-width: 900px; margin: 0 auto; text-align: center; }
        h1 { color: #e74c3c; font-size: 2.2em; margin-bottom: 5px; }
        p.subtitle { color: #7f8c8d; margin-bottom: 30px; }
        
        .grid-menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .card h3 { margin: 10px 0 5px; color: #2c3e50; }
        .card p { color: #7f8c8d; font-size: 0.9em; margin: 0; }
        .count { font-size: 1.5em; font-weight: bold; color: #e74c3c; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍩 Donuts & Drinks Inventory</h1>
        <p class="subtitle">Sistem Manajemen & Kasir Sweet Glaze</p>

        <div class="grid-menu">
            <a href="{{ route('products.index') }}" class="card">
                <h3>📦 Kelola Produk</h3>
                <p>Tambah, edit, hapus donat & minuman</p>
                <div class="count">{{ $totalProducts }} Produk</div>
            </a>

            <a href="{{ route('customers.index') }}" class="card">
                <h3>👥 Data Pelanggan</h3>
                <p>Kelola data pelanggan setia</p>
                <div class="count">{{ $totalCustomers }} Pelanggan</div>
            </a>

            <a href="{{ route('transactions.index') }}" class="card">
                <h3>🛒 Transaksi Kasir</h3>
                <p>Input penjualan & potong stok</p>
                <div class="count">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </a>
        </div>
    </div>
</body>
</html>