<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangRuangans extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'barang_id',
        'ruangan_id',
        'stok',
    ];

    public function barang() {
        return $this->belongsTo(Barangs::class,'barang_id');
    }

    public function ruangan() {
        return $this->belongsTo(Ruangans::class,'ruangan_id');
    }
}
