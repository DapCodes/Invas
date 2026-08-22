<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalians extends Model
{
    use HasFactory;

    protected $table = 'pengembalians';

    protected $fillable = [
        'kode_barang',
        'id_peminjam',
        'id_barang',
        'inventory_item_id',
        'jumlah',
        'selisih',
        'satuan_id',
        'tanggal_kembali',
        'nama_peminjam',
        'status',
        'kondisi',
        'ruangan_id',
        'id_user',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'selisih' => 'decimal:2',
        'tanggal_kembali' => 'date',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangans::class, 'ruangan_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barangs::class, 'id_barang');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'satuan_id');
    }

    public function peminjamans()
    {
        return $this->belongsTo(Peminjamans::class, 'id_peminjam');
    }

    public function details()
    {
        return $this->hasMany(PengembalianDetail::class, 'pengembalian_id');
    }
}