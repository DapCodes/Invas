<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianDetail extends Model
{
    use HasFactory;

    protected $table = 'pengembalian_details';

    protected $fillable = [
        'pengembalian_id',
        'peminjaman_detail_id',
        'barang_id',
        'inventory_item_id',
        'quantity',
        'selisih',
        'kondisi',
        'keterangan',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'selisih' => 'decimal:2',
    ];

    public function pengembalian()
    {
        return $this->belongsTo(Pengembalians::class, 'pengembalian_id');
    }

    public function peminjamanDetail()
    {
        return $this->belongsTo(PeminjamanDetail::class, 'peminjaman_detail_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barangs::class, 'barang_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
