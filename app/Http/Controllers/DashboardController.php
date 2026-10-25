<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;

class DashboardController extends Controller
{
    /**
     * Ringkasan bisnis yang dihitung langsung dari database (agregat),
     * bukan angka statis. Staff hanya melihat transaksi miliknya sendiri;
     * Admin/Owner melihat seluruh toko.
     */
    public function index()
    {
        $user = auth()->user();

        // Scope transaksi sesuai role (admin = semua, staff = miliknya)
        $sales = Sale::query()
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id));
        $salesCompleted = (clone $sales)->completed();
        $purchases = Purchase::query()
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id));

        $stats = [
            'products'        => Product::count(),
            'customers'       => Customer::count(),
            'low_stock'       => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'sales_today'     => (clone $salesCompleted)->whereDate('sold_at', today())->sum('total'),
            'sales_month'     => (clone $salesCompleted)
                                    ->whereMonth('sold_at', today()->month)
                                    ->whereYear('sold_at', today()->year)
                                    ->sum('total'),
            'sales_count'     => (clone $sales)->count(),
            'purchases_count' => (clone $purchases)->count(),
        ];

        // Tren penjualan 7 hari terakhir (penjualan selesai saja)
        $labels = [0 => 'Min', 1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab'];
        $chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = today()->subDays($i);
            $chart[] = [
                'label' => $labels[$day->format('w')],
                'total' => (clone $salesCompleted)->whereDate('sold_at', $day)->sum('total'),
                'today' => $i === 0,
            ];
        }
        $dayTotals = array_map('intval', array_column($chart, 'total'));
        $chartMax = max(1, max($dayTotals));

        // Produk bermasalah (restok) & penjualan terbaru
        $lowStockProducts = Product::whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')->orderBy('name')
            ->limit(6)->get();

        $recentSales = (clone $sales)
            ->with(['user', 'items'])
            ->latest('sold_at')->latest('id')
            ->limit(6)->get();

        return view('layouts.dashboard', compact(
            'stats', 'chart', 'chartMax', 'lowStockProducts', 'recentSales'
        ));
    }
}
