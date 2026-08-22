<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Pieces / Buah', 'symbol' => 'pcs', 'is_decimal' => false, 'description' => 'Satuan material kecil (RJ45, Patch Cord, dll.)'],
            ['name' => 'Unit', 'symbol' => 'unit', 'is_decimal' => false, 'description' => 'Satuan perangkat elektronik/peralatan (Router, Splicer, Monitor, dll.)'],
            ['name' => 'Meter', 'symbol' => 'meter', 'is_decimal' => true, 'description' => 'Satuan panjang kabel (Fiber Optik, Dropcore, LAN, dll.)'],
            ['name' => 'Roll / Gulungan', 'symbol' => 'roll', 'is_decimal' => false, 'description' => 'Satuan gulungan kabel / pita insulasi'],
            ['name' => 'Box / Kotak', 'symbol' => 'box', 'is_decimal' => false, 'description' => 'Satuan kotak connector / sleeve'],
            ['name' => 'Pack', 'symbol' => 'pack', 'is_decimal' => false, 'description' => 'Satuan kemasan pak / cable tie'],
            ['name' => 'Kilogram', 'symbol' => 'kg', 'is_decimal' => true, 'description' => 'Satuan berat kilogram'],
            ['name' => 'Gram', 'symbol' => 'gram', 'is_decimal' => true, 'description' => 'Satuan berat gram'],
            ['name' => 'Liter', 'symbol' => 'liter', 'is_decimal' => true, 'description' => 'Satuan volume cairan / alkohol pembersih fiber'],
            ['name' => 'Set / Paket', 'symbol' => 'set', 'is_decimal' => false, 'description' => 'Satuan set peralatan toolkit / obeng set'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['symbol' => $unit['symbol']], $unit);
        }
    }
}
