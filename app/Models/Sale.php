<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['user_id', 'customer_id', 'sale_number', 'total', 'status', 'sold_at'];

    protected $casts = [
        'sold_at' => 'datetime',
        'total'   => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items() { return $this->hasMany(SaleItem::class); }

    /**
     * True jika penjualan masih berstatus selesai (belum dibatalkan).
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Filter: hanya penjualan yang selesai (dipakai ringkasan/statistik).
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
