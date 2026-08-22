<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'PT Fiber Teknologi Nusantara',
                'code' => 'VND-FTN',
                'phone' => '022-7564123',
                'email' => 'sales@fiberteknologi.co.id',
                'address' => 'Jl. Telekomunikasi No. 88, Bandung',
                'description' => 'Distributor resmi kabel fiber optik, dropcore, dan perlengkapan FO',
            ],
            [
                'name' => 'PT Network Solution Indonesia',
                'code' => 'VND-NSI',
                'phone' => '021-8980123',
                'email' => 'info@networksolutions.co.id',
                'address' => 'Kawasan Industri MM2100 Blok C-12, Cikarang',
                'description' => 'Penyedia perangkat routing, switching MikroTik & Cisco enterprise',
            ],
            [
                'name' => 'PT Sinar Telekomunikasi',
                'code' => 'VND-ST',
                'phone' => '021-5256789',
                'email' => 'sales@sinartel.co.id',
                'address' => 'Jl. Gatot Subroto Kav. 45, Jakarta Selatan',
                'description' => 'Distributor perangkat GPON, OLT, ONT/ONU ZTE dan Huawei',
            ],
            [
                'name' => 'PT Mitra Infrastruktur Digital',
                'code' => 'VND-MID',
                'phone' => '022-7312900',
                'email' => 'support@mitradigital.id',
                'address' => 'Jl. Soekarno Hatta No. 590, Bandung',
                'description' => 'Penyedia alat ukur OTDR, Fusion Splicer, dan toolkit teknisi',
            ],
            [
                'name' => 'CV Solusi Jaringan',
                'code' => 'VND-SJ',
                'phone' => '022-2503456',
                'email' => 'order@solusijaringan.com',
                'address' => 'Jl. Dipatiukur No. 102, Bandung',
                'description' => 'Penyedia material konektivitas RJ45, patch cord, dan perlengkapan LAN',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::firstOrCreate(['code' => $vendor['code']], $vendor);
        }
    }
}
