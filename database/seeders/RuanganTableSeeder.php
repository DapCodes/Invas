<?php

namespace Database\Seeders;

use App\Models\Ruangans;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuanganTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $ruangans = [
            [
                'nama_ruangan' => 'Kafe Timeline',
                'deskripsi' => 'Area Kafe Timeline & Workshop Perangkat',
            ],
            [
                'nama_ruangan' => 'Kantor Bukadir & GWK (RV)',
                'deskripsi' => 'Kantor Utama & Ruang Operasional Teknisi ISP',
            ],
            [
                'nama_ruangan' => 'River Prawn',
                'deskripsi' => 'Gudang Cabang & Pusat Distribusi Material River Prawn',
            ],
        ];

        foreach ($ruangans as $ruangan) {
            Ruangans::firstOrCreate(
                ['nama_ruangan' => $ruangan['nama_ruangan']],
                $ruangan
            );
        }
    }
}
