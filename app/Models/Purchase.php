<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['supplier_id', 'user_id', 'purchase_number', 'total', 'status', 'purchased_at'];

    protected $casts = [
        'purchased_at' => 'datetime',
        'total'        => 'decimal:2',
    ];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(PurchaseItem::class); }

    /**
     * True jika pembelian masih berstatus selesai (belum dibatalkan).
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
