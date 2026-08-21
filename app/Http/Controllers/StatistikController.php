<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\Barangs;
use App\Models\Peminjamans;
use App\Models\Pengembalians;
use App\Models\User;
use App\Models\BarangMasuks;
use App\Models\Ruangans;
use App\Models\BarangKeluars;
use Carbon\Carbon;

Carbon::setLocale('id');


class StatistikController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->status_user === 'admin';

        $satuBulanLalu = Carbon::now()->subDays(7);
        $semua = Carbon::now()->subDays(1000);

       if ($isAdmin) {
            // Barang yang dibuat dalam 7 hari terakhir & status_barang > 0
            $barang = Barangs::where('created_at', '>=', $semua)
                ->count();

            $barangDetail = Barangs::where('created_at', '>=', $semua)
                ->where('stok', '>', 0)
                ->orderBy('stok', 'desc')
                ->paginate(5); // Hanya 5 barang per halaman

            $barangStok = Barangs::where('created_at', '>=', $semua)
                ->sum('stok');

        } else {
            // Hanya barang dengan status_barang sesuai user, dan status > 0
            $barang = Barangs::where('status_barang', $user->status_user)
                ->where('created_at', '>=', $satuBulanLalu)
                ->count();

            $barangDetail = Barangs::where('status_barang', $user->status_user)
                ->where('created_at', '>=', $satuBulanLalu)
                ->orderBy('stok', 'desc')
                ->paginate(5);;

            $barangStok = Barangs::where('status_barang', $user->status_user)
                ->where('created_at', '>=', $satuBulanLalu)
                ->sum('stok');
        }



        $peminjaman = Peminjamans::where('tanggal_pinjam', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->where('status', 'Sedang Dipinjam')
            ->count();
        
        
        
        $peminjamanDetail = Peminjamans::query()
            ->when(!$isAdmin, function ($query) use ($user) {
                if (strtolower($user->status_user) === 'umum') {
                    // User umum: hanya peminjaman yang barangnya berstatus Umum
                    $query->whereHas('barang', function ($q) {
                        $q->where('status_barang', 'Umum');
                    });
                } else {
                    // User lain: berdasarkan deskripsi ruangan
                    $query->whereHas('ruangan', function ($q) use ($user) {
                        $q->where('deskripsi', $user->status_user);
                    });
                }
            })
            ->orderBy('tanggal_pinjam', 'desc')
            ->get();





        $peminjamanStok = Peminjamans::where('tanggal_pinjam', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->where('status', 'Sedang Dipinjam')
            ->sum('jumlah');



        // Pengembalian (dalam 1 bulan terakhir)
        $pengembalian = Pengembalians::where('tanggal_kembali', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->count();


        
        $pengembalianStok = Pengembalians::where('tanggal_kembali', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->sum('jumlah');



        // Ruangan
        $ruangan = Ruangans::when(!$isAdmin, function ($query) use ($user) {
            $query->where('deskripsi', $user->status_user);
        })->count();



        // Barang Masuk (dalam 1 bulan terakhir)
        $barangMasuk = BarangMasuks::where('tanggal_masuk', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->count();



        // Barang Keluar (dalam 1 bulan terakhir)
        $barangKeluar = BarangKeluars::where('tanggal_keluar', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            })
            ->count();



        // Total Stok Masuk (dalam 1 bulan terakhir)
        $totalStokMasuk = BarangMasuks::where('tanggal_masuk', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('barang', function ($q) use ($user) {
                    $q->where('status_barang', $user->status_user);
                });
            })
            ->sum('jumlah');



        // Total Stok Keluar (dalam 1 bulan terakhir)
        $totalStokKeluar = BarangKeluars::where('tanggal_keluar', '>=', $satuBulanLalu)
            ->when(!$isAdmin, function ($query) use ($user) {
                $query->whereHas('barang', function ($q) use ($user) {
                    $q->where('status_barang', $user->status_user);
                });
            })
            ->sum('jumlah');



        // Ambil data 5 hari terakhir untuk chart
        $labels = [];
        $data = [];

        $now = Carbon::now();

        for ($i = 0; $i <= 6; $i++) {
            $tanggal = $now->copy()->subDays(6 - $i)->startOfDay(); // urut dari 4 hari lalu sampai hari ini
            $namaHari = $tanggal->translatedFormat('l');

            $labels[] = $namaHari;

            $jumlah = BarangMasuks::whereDate('tanggal_masuk', $tanggal)
                ->when(!$isAdmin, function ($query) use ($user) {
                    $query->whereHas('barang', function ($q) use ($user) {
                        $q->where('status_barang', $user->status_user);
                    });
                })
                ->sum('jumlah');

            $data[] = $jumlah;
        }

        $labels2 = [];
        $data2 = [];

        $now = Carbon::now();

        for ($i = 0; $i <= 6; $i++) {
            $tanggal = $now->copy()->subDays(6 - $i)->startOfDay(); // urut dari 4 hari lalu sampai hari ini
            $namaHari = $tanggal->translatedFormat('l');

            $labels2[] = $namaHari;

            $jumlah = BarangKeluars::whereDate('tanggal_keluar', $tanggal)
                ->when(!$isAdmin, function ($query) use ($user) {
                    $query->whereHas('ruangan', function ($q) use ($user) {
                        $q->where('deskripsi', $user->status_user);
                    });
                })
                ->sum('jumlah');

            $data2[] = $jumlah;
        }



        $total = $barang + $peminjaman + $pengembalian + $ruangan + $barangMasuk + $barangKeluar;

        $chartData = [
            'labels' => ['Barang', 'Ruangan', 'Barang Masuk', 'Barang Keluar', 'Peminjaman', 'Pengembalian'],
            'series' => [$barang, $ruangan, $barangMasuk, $barangKeluar, $peminjaman, $pengembalian],
            'pinjamkembali' => ['Peminjaman', 'Pengembalian'],
            'pinjamkembaliseries' => [$peminjaman, $pengembalian]
        ];

        return view('statistik', [
            'chartData'                => $chartData,
            'barang'                   => $barang,
            'barangStok'               => $barangStok,
            'barangDetail'             => $barangDetail,
            'peminjaman'               => $peminjaman,
            'peminjamanDetail'         => $peminjamanDetail,
            'peminjamanStok'           => $peminjamanStok,
            'pengembalian'             => $pengembalian,
            'pengembalianStok'         => $pengembalianStok,
            'ruangan'                  => $ruangan,
            'barangMasuk'              => $barangMasuk,
            'barangKeluar'             => $barangKeluar,
            'total'                    => $total,
            'totalStokMasuk'           => $totalStokMasuk,
            'totalStokKeluar'          => $totalStokKeluar,
            'stokChartLabels'          => $labels,
            'stokChartData'            => $data,
            'stokChartLabels2'         => $labels2,
            'stokChartData2'           => $data2,
        ]);

    }



}
