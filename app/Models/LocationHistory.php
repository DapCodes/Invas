<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationHistory extends Model
{
    use HasFactory;

    protected $table = 'location_histories';

    protected $fillable = [
        'inventory_item_id',
        'from_ruangan_id',
        'to_ruangan_id',
        'user_id',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function fromRuangan()
    {
        return $this->belongsTo(Ruangans::class, 'from_ruangan_id');
    }

    public function toRuangan()
    {
        return $this->belongsTo(Ruangans::class, 'to_ruangan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
