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
    public function run()
    {
        $ruangan = [];

        // Kelas X sampai XII
        foreach (['X', 'XI', 'XII'] as $kelas) {
            for ($i = 1; $i <= 3; $i++) {
                $ruangan[] = [
                    'nama_ruangan' => "Ruang Kelas $kelas-$i",
                    'deskripsi' => 'Ruang Kelas',
                ];
            }
        }

        // Laboratorium
        for ($i = 1; $i <= 3; $i++) {
            $ruangan[] = [
                'nama_ruangan' => "Laboratorium $i",
                'deskripsi' => 'Laboratorium Komputer',
            ];
        }

        // Bengkel
        for ($i = 1; $i <= 2; $i++) {
            $ruangan[] = [
                'nama_ruangan' => "Bengkel Praktikum $i",
                'deskripsi' => 'Bengkel Praktikum',
            ];
        }

        // Ruang Fasilitas Umum
        $fasilitas = ['Ruang Guru', 'Ruang BK', 'Perpustakaan', 'Unit Produksi', 'Mushola', 'Ruangan Osis', 'BLK', 'Gudang Utama'];
        foreach ($fasilitas as $nama) {
            $ruangan[] = [
                'nama_ruangan' => $nama,
                'deskripsi' => 'Fasilitas Umum',
            ];
        }

        DB::table('ruangans')->insert($ruangan);
    }
}
