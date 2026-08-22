<?php

namespace App\Http\Controllers;

use App\Models\Barangs;
use App\Models\BarangKeluars;
use App\Models\BarangMasuks;
use App\Models\InventoryItem;
use App\Models\Peminjamans;
use App\Models\Pengembalians;
use App\Models\Ruangans;
use App\Models\StockMovement;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;

Carbon::setLocale('id');

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $satuBulanLalu = Carbon::now()->subDays(30);
        $tujuhHariLalu = Carbon::now()->subDays(7);
        $today = Carbon::today();

        // 1. Master Counts
        $totalBarang = Barangs::where('is_active', true)->count();
        $totalSerialBarang = Barangs::where('is_active', true)->where('has_serial_number', true)->count();
        $totalNonSerialBarang = Barangs::where('is_active', true)->where('has_serial_number', false)->count();

        $totalSerialUnits = InventoryItem::whereNotIn('status', ['lost', 'damaged', 'depleted'])->count();
        $totalStockUnits = Barangs::where('is_active', true)->sum('stok');

        $totalRuangan = Ruangans::count();
        $totalVendor = Vendor::count();

        // 2. Loans & Overdue
        $activeLoansCount = Peminjamans::where('status', 'Sedang Dipinjam')->count();
        $overdueLoansCount = Peminjamans::where('status', 'Sedang Dipinjam')
            ->whereDate('tanggal_kembali', '<', $today)
            ->count();

        // 3. Transactions 30 days
        $totalMasukCount = BarangMasuks::where('tanggal_masuk', '>=', $satuBulanLalu)->count();
        $totalMasukQty = BarangMasuks::where('tanggal_masuk', '>=', $satuBulanLalu)->sum('jumlah');

        $totalKeluarCount = BarangKeluars::where('tanggal_keluar', '>=', $satuBulanLalu)->count();
        $totalKeluarQty = BarangKeluars::where('tanggal_keluar', '>=', $satuBulanLalu)->sum('jumlah');

        $totalPengembalianCount = Pengembalians::where('tanggal_kembali', '>=', $satuBulanLalu)->count();

        // 4. Recent Stock Movements
        $recentMovements = StockMovement::with(['barang.unit', 'inventoryItem', 'ruangan', 'user'])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // 5. Chart 7-day mutations
        $chartLabels = [];
        $chartMasuk = [];
        $chartKeluar = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D, d M');
            $chartMasuk[] = (float) BarangMasuks::whereDate('tanggal_masuk', $date)->sum('jumlah');
            $chartKeluar[] = (float) BarangKeluars::whereDate('tanggal_keluar', $date)->sum('jumlah');
        }

        $chartData = [
            'labels' => ['Master Barang', 'Unit Serial', 'Ruangan', 'Masuk (30h)', 'Keluar (30h)', 'Pinjam Aktif'],
            'series' => [$totalBarang, $totalSerialUnits, $totalRuangan, $totalMasukCount, $totalKeluarCount, $activeLoansCount],
        ];

        // Aliases for legacy view variables if referenced
        $barang = $totalBarang;
        $barangStok = $totalStockUnits;
        $peminjaman = $activeLoansCount;
        $peminjamanStok = Peminjamans::where('status', 'Sedang Dipinjam')->sum('jumlah');
        $pengembalian = $totalPengembalianCount;
        $pengembalianStok = Pengembalians::where('tanggal_kembali', '>=', $satuBulanLalu)->sum('jumlah');
        $ruangan = $totalRuangan;
        $barangMasuk = $totalMasukCount;
        $barangKeluar = $totalKeluarCount;
        $total = $totalBarang + $activeLoansCount + $totalPengembalianCount + $totalRuangan + $totalMasukCount + $totalKeluarCount;
        $totalStokMasuk = $totalMasukQty;
        $totalStokKeluar = $totalKeluarQty;

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
            'totalStokKeluar',
            'totalSerialBarang',
            'totalNonSerialBarang',
            'totalSerialUnits',
            'overdueLoansCount',
            'recentMovements',
            'chartLabels',
            'chartMasuk',
            'chartKeluar'
        ));
    }
}
