<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'name',
        'symbol',
        'is_decimal',
        'description',
    ];

    protected $casts = [
        'is_decimal' => 'boolean',
    ];

    public function barangs()
    {
        return $this->hasMany(Barangs::class, 'satuan_id');
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'satuan_id');
    }
}
