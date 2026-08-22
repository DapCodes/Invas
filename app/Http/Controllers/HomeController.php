<?php
namespace App\Http\Controllers;

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

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $satuBulanLalu = Carbon::now()->subDays(7);

        // Barang yang dibuat dalam 7 hari terakhir
        $barang = Barangs::where('created_at', '>=', $satuBulanLalu)->count();
        $barangStok = Barangs::where('created_at', '>=', $satuBulanLalu)->sum('stok');

        $peminjaman = Peminjamans::where('tanggal_pinjam', '>=', $satuBulanLalu)
            ->where('status', 'Sedang Dipinjam')
            ->count();
        
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

        $total = $barang + $peminjaman + $pengembalian + $ruangan + $barangMasuk + $barangKeluar;

        $chartData = [
            'labels' => ['Barang', 'Ruangan', 'Barang Masuk', 'Barang Keluar', 'Peminjaman', 'Pengembalian'],
            'series' => [$barang, $ruangan, $barangMasuk, $barangKeluar, $peminjaman, $pengembalian],
            'pinjamkembali' => ['Peminjaman', 'Pengembalian'],
            'pinjamkembaliseries' => [$peminjaman, $pengembalian]
        ];

        return view('home', compact(
            'chartData',
            'barang',
            'barangStok',
            'peminjaman',
            'peminjamanStok',
            'pengembalian',
            'pengembalianStok',
            'ruangan',
            'barangMasuk',
            'barangKeluar',
            'total',
            'totalStokMasuk',
            'totalStokKeluar'
        ));
    }
}
