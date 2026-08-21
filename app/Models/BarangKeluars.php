<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluars extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'jumlah',
        'tanggal_keluar',
        'keterangan',
        'id_barang',
        'ruangan_id',
        'id_user',
    ];

    public $timestamps = true;

    public function user() {
        return $this->belongsTo(User::class,'id_user');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangans::class, 'ruangan_id');
    }


    public function barang() {
        return $this->belongsTo(Barangs::class,'id_barang');
    }
}
