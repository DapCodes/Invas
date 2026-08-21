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

        $jurusan = [
            'RPL' => 3,
            'TBSM' => 2,
            'TKRO' => 2,
        ];

        // Kelas X sampai XII
        foreach (['X', 'XI', 'XII'] as $kelas) {
            foreach ($jurusan as $namaJurusan => $jumlahKelas) {
                for ($i = 1; $i <= $jumlahKelas; $i++) {
                    $ruangan[] = [
                        'nama_ruangan' => "$kelas $namaJurusan $i",
                        'deskripsi' => $namaJurusan,
                    ];
                }
            }
        }

        // Lab RPL 1-3
        for ($i = 1; $i <= 3; $i++) {
            $ruangan[] = [
                'nama_ruangan' => "Lab RPL $i",
                'deskripsi' => 'RPL',
            ];
        }

        // Bengkel TBSM 1-2
        for ($i = 1; $i <= 2; $i++) {
            $ruangan[] = [
                'nama_ruangan' => "Bengkel TBSM $i",
                'deskripsi' => 'TBSM',
            ];
        }

        // Bengkel TKRO 1-2
        for ($i = 1; $i <= 2; $i++) {
            $ruangan[] = [
                'nama_ruangan' => "Bengkel TKRO $i",
                'deskripsi' => 'TKRO',
            ];
        }

        // Ruang Umum
        $umum = ['Ruang Guru', 'Ruang BK', 'Perpustakaan', 'UP RPL', 'UP TKRO', 'UP TBSM', 'Mushola', 'Ruangan Osis', 'Lab Informatika', 'BLK', 'Gudang'];
        foreach ($umum as $nama) {
            $ruangan[] = [
                'nama_ruangan' => $nama,
                'deskripsi' => 'Umum',
            ];
        }

        DB::table('ruangans')->insert($ruangan);
    }
}
