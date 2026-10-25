<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Kelola master data (kategori, dll): hanya Admin/Owner.
        Gate::define('manage-master', function (User $user) {
            return $user->isAdmin();
        });

        // Kelola pengguna & role: hanya Admin/Owner.
        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        // Batalkan transaksi (pembelian/penjualan): hanya Admin/Owner.
        Gate::define('cancel-transaction', function (User $user) {
            return $user->isAdmin();
        });

        // Catat & lihat penjualan: Admin dan Staff.
        Gate::define('manage-sales', function (User $user) {
            return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF]);
        });

        // Kelola pelanggan: Admin dan Staff.
        Gate::define('manage-customers', function (User $user) {
            return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF]);
        });
    }
}
