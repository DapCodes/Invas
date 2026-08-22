<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';

    protected $fillable = [
        'barang_id',
        'inventory_item_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'ruangan_id',
        'user_id',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'quantity_before' => 'decimal:2',
        'quantity_after' => 'decimal:2',
        'tanggal' => 'datetime',
    ];

    public function barang()
    {
        return $this->belongsTo(Barangs::class, 'barang_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangans::class, 'ruangan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
