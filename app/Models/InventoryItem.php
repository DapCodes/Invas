<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_items';

    protected $fillable = [
        'barang_id',
        'serial_number',
        'initial_quantity',
        'current_quantity',
        'satuan_id',
        'status',
        'ruangan_id',
        'id_user',
        'tanggal_masuk',
        'tanggal_keluar',
        'keterangan',
    ];

    protected $casts = [
        'initial_quantity' => 'decimal:2',
        'current_quantity' => 'decimal:2',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function barang()
    {
        return $this->belongsTo(Barangs::class, 'barang_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'satuan_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangans::class, 'ruangan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'inventory_item_id');
    }

    public function locationHistories()
    {
        return $this->hasMany(LocationHistory::class, 'inventory_item_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('current_quantity', '>', 0);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['lost', 'damaged', 'depleted']);
    }
}
