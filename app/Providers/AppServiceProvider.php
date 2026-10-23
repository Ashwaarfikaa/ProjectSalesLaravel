<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Tampilan pagination bertema Sweet Glaze (tanpa Tailwind/Bootstrap).
        Paginator::defaultView('vendor.pagination.glaze');

        // Pemakaian: @rupiah($angka)
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format((float) ({$expression}), 0, ',', '.'); ?>";
        });
    }
}