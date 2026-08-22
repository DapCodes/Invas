<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanDetail extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_details';

    protected $fillable = [
        'peminjaman_id',
        'barang_id',
        'inventory_item_id',
        'quantity',
        'returned_quantity',
        'status',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'returned_quantity' => 'decimal:2',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjamans::class, 'peminjaman_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barangs::class, 'barang_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function pengembalianDetails()
    {
        return $this->hasMany(PengembalianDetail::class, 'peminjaman_detail_id');
    }
}
