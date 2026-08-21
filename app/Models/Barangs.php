<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangs extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama',
        'merek',
        'foto',
        'stok',
        'status_barang',
        'id_user',
        'vendor_id',
        'serial_number',
    ];

    public $timestamps = true;

    public function users() {
        return $this->belongsTo(User::class,'id_user');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function barangmasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }
    public function barangkeluar()
    {
        return $this->hasMany(BarangKeluar::class);
    }
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }
    public function pengembalian()
    {
        return $this->hasMany(Pengembalian::class);
    }
    public function barangruangan()
    {
        return $this->hasMany(BarangRuangans::class);
    }

    public function user() {
        return $this->belongsTo(User::class,'id_user');
    }

    public function deleteImage(){
        if($this->cover && file_exists(public_path('image/barang' . $this->cover))) {
            return unlink(public_path('image/barang' . $this->cover));
        }
    }
}
