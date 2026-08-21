<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';

    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'address',
        'description',
    ];

    public $timestamps = true;

    public function barangs()
    {
        return $this->hasMany(Barangs::class, 'vendor_id');
    }
}
