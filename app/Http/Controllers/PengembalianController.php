<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengembalians;
use App\Models\Barangs;
use App\Models\Peminjamans;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Exports\PengembalianExport;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

Carbon::setLocale('id');

class PengembalianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan data pengembalian, filter, dan export
     */
public function index(Request $request)
{
    $user = Auth::user();

    $keyword = $request->input('search');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $exportType = $request->input('export');

    // Query awal
    $query = Pengembalians::with(['barang', 'ruangan', 'user'])
        ->where('status', 'Sudah Dikembalikan')
        ->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('barang', function ($q2) use ($keyword) {
                    $q2->where('nama', 'like', "%$keyword%")
                        ->orWhere('merek', 'like', "%$keyword%")
                        ->orWhere('status_barang', 'like', "%$keyword%");
                })
                ->orWhereHas('ruangan', function ($q2) use ($keyword) {
                    $q2->where('nama_ruangan', 'like', "%$keyword%");
                })
                ->orWhereHas('user', function ($q2) use ($keyword) {
                    $q2->where('name', 'like', "%$keyword%");
                });
            });
        })
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal_kembali', [$startDate, $endDate]);
        })
        ->when($startDate && !$endDate, function ($query) use ($startDate) {
            $query->whereDate('tanggal_kembali', '>=', $startDate);
        })
        ->when(!$startDate && $endDate, function ($query) use ($endDate) {
            $query->whereDate('tanggal_kembali', '<=', $endDate);
        })
        ->when(strtolower($user->status_user) !== 'admin', function ($query) use ($user) {
            if (strtolower($user->status_user) === 'umum') {
                // User umum hanya boleh melihat pengembalian barang dengan status "Umum"
                $query->whereHas('barang', function ($q) {
                    $q->where('status_barang', 'Umum');
                });
            } else {
                // User lainnya hanya melihat ruangan sesuai deskripsi mereka
                $query->whereHas('ruangan', function ($q) use ($user) {
                    $q->where('deskripsi', $user->status_user);
                });
            }
        });

    // EXPORT
    if ($exportType) {
        $dataExport = $query->get();

        if ($exportType === 'excel') {
            return Excel::download(new PengembalianExport($dataExport), 'laporan-data-pengembalian.xlsx');
        } elseif ($exportType === 'pdf') {
            $pdf = Pdf::loadView('pdf.pengembalian', ['pengembalian' => $dataExport]);
            return $pdf->download('laporan-data-pengembalian.pdf');
        }
    }

    // PAGINATION
    $pengembalian = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

    return view('pengembalian.index', compact('pengembalian', 'keyword', 'startDate', 'endDate'));
}


    /**
     * Detail pengembalian
     */
    public function show($id)
    {
        $pengembalian = Pengembalians::findOrFail($id);
        $barang = Barangs::findOrFail($pengembalian->id_barang);
        return view('pengembalian.show', compact('pengembalian', 'barang'));
    }

    public function destroy($id)
    {
        $pengembalian = Pengembalians::findOrFail($id);
    
        $pengembalian->delete();
        Alert::success('Dihapus!', 'Data Berhasil Dihapus');
        return redirect()->route('pengembalian.index');
    }
}
