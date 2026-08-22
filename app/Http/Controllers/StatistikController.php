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
        $satuBulanLalu = Carbon::now()->subDays(7);
        $semua = Carbon::now()->subDays(1000);

        $barang = Barangs::where('created_at', '>=', $semua)->count();

        $barangDetail = Barangs::where('created_at', '>=', $semua)
            ->where('stok', '>', 0)
            ->orderBy('stok', 'desc')
            ->paginate(5);

        $barangStok = Barangs::where('created_at', '>=', $semua)->sum('stok');

        $peminjaman = Peminjamans::where('tanggal_pinjam', '>=', $satuBulanLalu)
            ->where('status', 'Sedang Dipinjam')
            ->count();

        $peminjamanDetail = Peminjamans::orderBy('tanggal_pinjam', 'desc')->get();

        $peminjamanStok = Peminjamans::where('tanggal_pinjam', '>=', $satuBulanLalu)
            ->where('status', 'Sedang Dipinjam')
            ->sum('jumlah');

        // Pengembalian (dalam 1 bulan terakhir)
        $pengembalian = Pengembalians::where('tanggal_kembali', '>=', $satuBulanLalu)->count();

        $pengembalianStok = Pengembalians::where('tanggal_kembali', '>=', $satuBulanLalu)->sum('jumlah');

        // Ruangan
        $ruangan = Ruangans::count();

        // Barang Masuk (dalam 1 bulan terakhir)
        $barangMasuk = BarangMasuks::where('tanggal_masuk', '>=', $satuBulanLalu)->count();

        // Barang Keluar (dalam 1 bulan terakhir)
        $barangKeluar = BarangKeluars::where('tanggal_keluar', '>=', $satuBulanLalu)->count();

        // Total Stok Masuk (dalam 1 bulan terakhir)
        $totalStokMasuk = BarangMasuks::where('tanggal_masuk', '>=', $satuBulanLalu)->sum('jumlah');

        // Total Stok Keluar (dalam 1 bulan terakhir)
        $totalStokKeluar = BarangKeluars::where('tanggal_keluar', '>=', $satuBulanLalu)->sum('jumlah');

        // Ambil data 7 hari terakhir untuk chart barang masuk
        $labels = [];
        $data = [];
        $now = Carbon::now();

        for ($i = 0; $i <= 6; $i++) {
            $tanggal = $now->copy()->subDays(6 - $i)->startOfDay();
            $namaHari = $tanggal->translatedFormat('l');

            $labels[] = $namaHari;

            $jumlah = BarangMasuks::whereDate('tanggal_masuk', $tanggal)->sum('jumlah');

            $data[] = $jumlah;
        }

        // Ambil data 7 hari terakhir untuk chart barang keluar
        $labels2 = [];
        $data2 = [];
        $now = Carbon::now();

        for ($i = 0; $i <= 6; $i++) {
            $tanggal = $now->copy()->subDays(6 - $i)->startOfDay();
            $namaHari = $tanggal->translatedFormat('l');

            $labels2[] = $namaHari;

            $jumlah = BarangKeluars::whereDate('tanggal_keluar', $tanggal)->sum('jumlah');

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
