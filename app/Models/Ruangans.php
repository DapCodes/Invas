<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangans extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'nama_ruangan',
        'deskripsi',
     ];

    public $timestamps = true;

    public function barangruangan()
    {
        return $this->hasMany(BarangRuangans::class, 'ruangan_id');
    }

}
