<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangRuangans extends Model
{
    use HasFactory;

    protected $table = 'barang_ruangans';

    protected $fillable = [
        'barang_id',
        'ruangan_id',
        'stok',
    ];

    protected $casts = [
        'stok' => 'decimal:2',
    ];

    public function barang()
    {
        return $this->belongsTo(Barangs::class, 'barang_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangans::class, 'ruangan_id');
    }
}
