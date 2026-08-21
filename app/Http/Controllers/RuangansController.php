<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use App\Models\Ruangans;
use Illuminate\Http\Request;
use App\Exports\RuanganExport;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class RuangansController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->input('search');
        $exportType = $request->input('export');
        $statusFilter = $request->input('status'); // Ambil filter status dari request

        $ruanganQuery = Ruangans::query();

        // Filter pencarian (nama_ruangan dan deskripsi)
        if ($keyword) {
            $ruanganQuery->where(function ($query) use ($keyword) {
                $query->where('nama_ruangan', 'like', "%$keyword%")
                    ->orWhere('deskripsi', 'like', "%$keyword%");
            });
        }

        // Filter status ruangan berdasarkan select option
        if ($statusFilter) {
            $ruanganQuery->where('deskripsi', $statusFilter);
        }

        // Filter berdasarkan status_user jika bukan admin (relasi barang)
        if ($user->status_user !== 'admin') {
            $ruanganQuery->whereHas('barangruangan.barang', function ($query) use ($user) {
                $query->where('status_barang', $user->status_user);
            });
        }

        // Ambil data untuk export
        if ($exportType) {
            $ruangan = $ruanganQuery->get(); // Ambil semua data untuk export

            if ($exportType == 'excel') {
                return Excel::download(new RuanganExport($ruangan), 'laporan-data-ruangan.xlsx');
            }

            if ($exportType == 'pdf') {
                $pdf = Pdf::loadView('pdf.ruangan', ['ruangan' => $ruangan]);
                return $pdf->download('laporan-data-ruangan.pdf');
            }
        }

        // Ambil data ruangan dengan pagination
        $ruangan = $ruanganQuery->orderBy('nama_ruangan', 'asc')->paginate(10);

        return view('ruangan.index', compact('ruangan', 'keyword', 'statusFilter'));
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
        ]);

        // Simpan ke database
        Ruangans::create([
            'nama_ruangan' => $request->nama_ruangan,
            'deskripsi' => $request->deskripsi,
        ]);

        Alert::success('Berhasil!', 'Data ruangan berhasil disimpan.');
        return redirect()->route('ruangan.index');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
        ]);

        // Ambil data ruangan berdasarkan ID
        $ruangan = Ruangans::findOrFail($id);

        // Update data menggunakan data yang sudah divalidasi
        $ruangan->update($validated);

        Alert::success('Berhasil!', 'Data ruangan berhasil diperbarui.');
        return redirect()->route('ruangan.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ruangans  $ruangans
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ruangan = Ruangans::findOrFail($id);

        $ruangan->delete();
        Alert::success('Dihapus!', 'Data Berhasil Dihapus');
        return redirect()->route('ruangan.index');
    }
}
