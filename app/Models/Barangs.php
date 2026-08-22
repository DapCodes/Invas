<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangs extends Model
{
    use HasFactory;

    protected $table = 'barangs';

    protected $fillable = [
        'kode_barang',
        'nama',
        'merek',
        'deskripsi',
        'foto',
        'stok',
        'satuan_id',
        'id_user',
        'vendor_id',
        'serial_number',
        'has_serial_number',
        'is_active',
    ];

    protected $casts = [
        'has_serial_number' => 'boolean',
        'is_active' => 'boolean',
        'stok' => 'decimal:2',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'satuan_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Unit::class, 'satuan_id');
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'barang_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'barang_id');
    }

    public function barangmasuk()
    {
        return $this->hasMany(BarangMasuks::class, 'id_barang');
    }

    public function barangkeluar()
    {
        return $this->hasMany(BarangKeluars::class, 'id_barang');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjamans::class, 'id_barang');
    }

    public function pengembalian()
    {
        return $this->hasMany(Pengembalians::class, 'id_barang');
    }

    public function barangruangan()
    {
        return $this->hasMany(BarangRuangans::class, 'barang_id');
    }

    public function deleteImage()
    {
        if ($this->foto && file_exists(public_path('image/barang/' . $this->foto))) {
            return unlink(public_path('image/barang/' . $this->foto));
        }
    }
}
