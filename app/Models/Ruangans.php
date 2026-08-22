<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangans extends Model
{
    use HasFactory;

    protected $table = 'ruangans';

    protected $fillable = [
        'nama_ruangan',
        'deskripsi',
    ];

    public $timestamps = true;

    public function barangRuangan()
    {
        return $this->hasMany(BarangRuangans::class, 'ruangan_id');
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'ruangan_id');
    }
}
