<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuks extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_barang',
        'jumlah',
        'tanggal_masuk',
        'keterangan',
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
    
    public function barang()
    {
        return $this->belongsTo(Barangs::class, 'id_barang');
    }


    public function deleteImage(){
        if($this->cover && file_exists(public_path('image/barang-masuk' . $this->cover))) {
            return unlink(public_path('image/barang-masuk' . $this->cover));
        }
    }
}
