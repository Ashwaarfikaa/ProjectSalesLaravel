<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_STAFF = 'staff';

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    public function purchases() { return $this->hasMany(Purchase::class); }
    public function sales() { return $this->hasMany(Sale::class); }
    public function isAdmin(): bool { return $this->role === self::ROLE_ADMIN; }

    /**
     * Label role untuk ditampilkan di UI (sidebar dsb).
     */
    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Admin / Owner',
            self::ROLE_STAFF => 'Staff Kasir',
            default => ucfirst($this->role),
        };
    }
}
