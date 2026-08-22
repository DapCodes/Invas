<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjamans extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    protected $fillable = [
        'kode_barang',
        'id_barang',
        'inventory_item_id',
        'jumlah',
        'satuan_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'nama_peminjam',
        'status',
        'ruangan_id',
        'id_user',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_pinjam' => 'date',
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

    public function pengembalian()
    {
        return $this->hasMany(Pengembalians::class, 'id_peminjam');
    }

    public function details()
    {
        return $this->hasMany(PeminjamanDetail::class, 'peminjaman_id');
    }
}
