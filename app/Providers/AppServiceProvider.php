<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Directive Blade: @rupiah($value) -> "Rp 1.000.000"
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format((float) ({$expression}), 0, ',', '.'); ?>";
        });

        // Tampilan pagination khas tema SummitGear (pills hijau).
        Paginator::defaultView('pagination::summit');
    }
}
