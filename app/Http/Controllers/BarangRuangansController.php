<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Barangs;
use App\Models\BarangMasuks;
use App\Models\Ruangans;
use App\Models\BarangRuangans;
use RealRashid\SweetAlert\Facades\Alert;
use App\Exports\BarangRuanganExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

\Carbon\Carbon::setLocale('id');

class BarangRuangansController extends Controller
{

    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $exportType = $request->input('export');
        $byClass = $request->input('byClass');

        // Query utama barang ruangan
        $barangRuanganQuery = BarangRuangans::with(['ruangan', 'barang'])
            ->join('barangs', 'barang_ruangans.barang_id', '=', 'barangs.id')
            ->join('ruangans', 'barang_ruangans.ruangan_id', '=', 'ruangans.id')
            ->select('barang_ruangans.*')
            ->where('barang_ruangans.stok', '>', 0)
            ->orderBy('ruangans.nama_ruangan', 'asc');

        // Filter berdasarkan kelas/ruangan
        if ($byClass) {
            $barangRuanganQuery->where('barang_ruangans.ruangan_id', $byClass);
        }

        // Filter pencarian
        if ($keyword) {
            $barangRuanganQuery->where(function ($query) use ($keyword) {
                $query->whereHas('ruangan', function ($q) use ($keyword) {
                    $q->where('nama_ruangan', 'like', "%$keyword%")
                        ->orWhere('deskripsi', 'like', "%$keyword%");
                })->orWhereHas('barang', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%$keyword%")
                        ->orWhere('merek', 'like', "%$keyword%");
                });
            });
        }

        // Export jika diminta
        if ($exportType) {
            $barangRuangan = $barangRuanganQuery->get();

            if ($exportType == 'excel') {
                return Excel::download(new BarangRuanganExport($barangRuangan), 'laporan-barang-ruangan.xlsx');
            }

            if ($exportType == 'pdf') {
                $pdf = Pdf::loadView('pdf.barangruangan', ['barangRuangan' => $barangRuangan]);
                return $pdf->download('laporan-barang-ruangan.pdf');
            }
        }

        // Data paginasi
        $barangRuangan = $barangRuanganQuery->paginate(10)->withQueryString();

        // Ambil daftar ruangan untuk filter dropdown
        $ruangan = Ruangans::whereHas('barangRuangan', function ($query) {
            $query->where('stok', '>', 0);
        })
        ->orderBy('nama_ruangan')
        ->get();

        return view('barangruangan.index', compact('barangRuangan', 'ruangan', 'keyword', 'byClass'));
    }

    public function show($id)
    {
        $barangRuangan = BarangRuangans::findOrFail($id);
        return view('barangruangan.show', compact('barangRuangan'));
    }
}
