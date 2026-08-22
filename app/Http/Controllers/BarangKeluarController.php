<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\BarangKeluars;
use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\Ruangans;
use App\Exports\BarangKeluarExport;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;
Carbon::setLocale('id');

class BarangKeluarController extends Controller
{

    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $exportType = $request->input('export');

        $query = BarangKeluars::with(['barang', 'ruangan', 'user'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->whereHas('barang', function ($q) use ($keyword) {
                        $q->where('nama', 'like', "%$keyword%")
                        ->orWhere('merek', 'like', "%$keyword%");
                    })
                    ->orWhereHas('ruangan', function ($q) use ($keyword) {
                        $q->where('nama_ruangan', 'like', "%$keyword%");
                    })
                    ->orWhereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    });
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_keluar', [$startDate, $endDate]);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                $query->whereDate('tanggal_keluar', '>=', $startDate);
            })
            ->when(!$startDate && $endDate, function ($query) use ($endDate) {
                $query->whereDate('tanggal_keluar', '<=', $endDate);
            });

        // Ekspor jika diminta
        $barangKeluar = $query->get();

        if ($exportType === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\BarangKeluarExport($barangKeluar),
                'laporan-data-barangkeluar.xlsx'
            );
        }

        if ($exportType === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.barangKeluar', ['barangKeluar' => $barangKeluar]);
            return $pdf->download('laporan-data-barangkeluar.pdf');
        }

        // Paginate hasil akhir
        $barangKeluar = $query->paginate(10)->withQueryString();

        return view('barangkeluar.index', compact('barangKeluar', 'keyword', 'startDate', 'endDate'));
    }



    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $barang = Barangs::all();
        $ruanganIdsWithBarang = BarangRuangans::distinct()->pluck('ruangan_id');
        $ruangan = Ruangans::whereIn('id', $ruanganIdsWithBarang)->get();

        return view('barangkeluar.create', compact('barang', 'ruangan'));
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'tanggal_keluar' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'id_barang' => 'required|exists:barangs,id',
        ],
        [
            'jumlah.required' => 'Jumlah barang harus diisi',
            'jumlah.integer' => 'Jumlah barang harus berupa angka',
            'jumlah.min' => 'Jumlah barang minimal 1',
            'tanggal_keluar.required' => 'Tanggal keluar harus diisi',
            'tanggal_keluar.date' => 'Format tanggal tidak valid',
            'keterangan.required' => 'Keterangan harus diisi',
            'keterangan.string' => 'Keterangan harus berupa teks',
            'keterangan.max' => 'Keterangan maksimal 255 karakter',
            'id_barang.required' => 'ID barang harus diisi',
            'id_barang.exists' => 'ID barang tidak ditemukan',
        ]);

        try {
            DB::beginTransaction();

            // Validasi dan pengurangan stok ruangan
            if ($request->deskripsi) {
                $barangRuangan = BarangRuangans::where('barang_id', $request->id_barang)
                    ->where('ruangan_id', $request->deskripsi)
                    ->first();

                if ($barangRuangan) {
                    if ($barangRuangan->stok < $request->jumlah) {
                        Alert::error('Gagal!', 'Jumlah melebihi stok barang');
                        return back();
                    }

                    $barangRuangan->stok -= $request->jumlah;
                    $barangRuangan->save();
                } else {
                    Alert::error('Gagal!', 'Barang tidak tersedia di ruangan ini.');
                    return back();
                }
            }

            // Cek dan kurangi stok dari tabel utama `barangs`
            $barang = Barangs::findOrFail($request->id_barang);
            if ($barang->stok < $request->jumlah) {
                Alert::error('Gagal!', 'Stok tidak mencukupi');
                return back();
            }

            $barang->stok -= $request->jumlah;
            $barang->save();

            // Generate kode_barang keluar
            $lastRecord = BarangKeluars::latest('id')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $kodeBarang = 'BK-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

            // Simpan data barang keluar
            $barangKeluar = new BarangKeluars();
            $barangKeluar->kode_barang = $kodeBarang;
            $barangKeluar->jumlah = $request->jumlah;
            $barangKeluar->tanggal_keluar = $request->tanggal_keluar;
            $barangKeluar->keterangan = $request->keterangan;
            $barangKeluar->id_barang = $request->id_barang;
            $barangKeluar->ruangan_id = $request->deskripsi ?? null;

            $userId = Auth::user();
            $barangKeluar->id_user = $userId->id;

            $barangKeluar->save();

            DB::commit();

            Alert::success('Berhasil!', 'Data Berhasil Ditambahkan');
            return redirect()->route('brg-keluar.index');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $barangKeluar = BarangKeluars::findOrFail($id);
        return view('barangkeluar.show', compact('barangKeluar'));
    }


    public function edit($id)
    {
        $barangKeluar = BarangKeluars::findOrFail($id);
        $barang = Barangs::all();
        $ruangan = Ruangans::all();

        // Cek keterkaitan barang & ruangan untuk data yang akan diedit
        $barangRuangan = BarangRuangans::where('barang_id', $barangKeluar->id_barang)
            ->where('ruangan_id', $barangKeluar->ruangan_id)
            ->first();

        return view('barangkeluar.edit', compact('barang', 'ruangan', 'barangKeluar', 'barangRuangan'));
    }



    
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'tanggal_keluar' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
            'id_barang' => 'required|exists:barangs,id',
            'ruangan_id' => 'required|exists:ruangans,id',
        ], [
            'jumlah.required' => 'Jumlah barang harus diisi',
            'jumlah.integer' => 'Jumlah barang harus berupa angka',
            'jumlah.min' => 'Jumlah barang minimal 1',
            'tanggal_keluar.required' => 'Tanggal keluar harus diisi',
            'tanggal_keluar.date' => 'Format tanggal tidak valid',
            'keterangan.string' => 'Keterangan harus berupa teks',
            'keterangan.max' => 'Keterangan maksimal 255 karakter',
            'id_barang.required' => 'ID barang harus diisi',
            'id_barang.exists' => 'ID barang tidak ditemukan',
            'ruangan_id.required' => 'Ruangan harus dipilih',
            'ruangan_id.exists' => 'Ruangan tidak valid',
        ]);

        // Ambil data lama
        $barangKeluar = BarangKeluars::findOrFail($id);
        $barangLama = Barangs::findOrFail($barangKeluar->id_barang);

        // Kembalikan stok barang utama
        $barangLama->stok += $barangKeluar->jumlah;
        $barangLama->save();

        // Kembalikan stok ke barang_ruangan lama
        $oldBarangRuangan = BarangRuangans::where('barang_id', $barangKeluar->id_barang)
            ->where('ruangan_id', $barangKeluar->ruangan_id)
            ->first();

        if ($oldBarangRuangan) {
            $oldBarangRuangan->stok += $barangKeluar->jumlah;
            $oldBarangRuangan->save();
        }

        // Cek stok barang baru
        $barangBaru = Barangs::findOrFail($request->id_barang);
        if ($barangBaru->stok < $request->jumlah) {
            Alert::error('Gagal!', 'Stok barang tidak mencukupi untuk dikeluarkan.');
            return redirect()->back();
        }

        // Kurangi stok barang utama baru
        $barangBaru->stok -= $request->jumlah;
        $barangBaru->save();

        // Kurangi stok dari barangRuangan baru
        $newBarangRuangan = BarangRuangans::where('barang_id', $request->id_barang)
            ->where('ruangan_id', $request->ruangan_id)
            ->first();

        if (!$newBarangRuangan || $newBarangRuangan->stok < $request->jumlah) {
            Alert::error('Gagal!', 'Stok barang di ruangan tidak mencukupi.');
            return redirect()->back();
        }

        $newBarangRuangan->stok -= $request->jumlah;
        $newBarangRuangan->save();

        // Simpan perubahan pada barang keluar
        $barangKeluar->id_barang = $request->id_barang;
        $barangKeluar->jumlah = $request->jumlah;
        $barangKeluar->tanggal_keluar = $request->tanggal_keluar;
        $barangKeluar->keterangan = $request->keterangan;
        $barangKeluar->ruangan_id = $request->ruangan_id;

        $barangKeluar->id_user = $barangKeluar->id_user;        

        $barangKeluar->save();

        Alert::success('Berhasil!', 'Data berhasil diperbarui.');
        return redirect()->route('brg-keluar.index');
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $barangKeluar = BarangKeluars::findOrFail($id);
        $barang = Barangs::findOrFail($barangKeluar->id_barang);

        // 1. Kembalikan stok ke gudang
        $barang->stok += $barangKeluar->jumlah;
        $barang->save();

        // 2. Kembalikan stok ke ruangan
        $barangRuangan = BarangRuangans::where('barang_id', $barangKeluar->id_barang)
                        ->where('ruangan_id', $barangKeluar->ruangan_id)
                        ->first();

        if ($barangRuangan) {
            // Tambah stok ke yang sudah ada
            $barangRuangan->stok += $barangKeluar->jumlah;
            $barangRuangan->save();
        } else {
            // Buat data baru jika belum ada
            BarangRuangans::create([
                'barang_id'   => $barangKeluar->id_barang,
                'ruangan_id'  => $barangKeluar->ruangan_id,
                'stok'        => $barangKeluar->jumlah,
            ]);
        }

        // 3. Hapus data barang keluar
        $barangKeluar->delete();

        Alert::success('Berhasil!', 'Data berhasil dihapus')->autoClose(1500);
        return redirect()->route('brg-keluar.index');
    }


    public function getBarangByRuangan($ruanganId)
    {
        $barang = BarangRuangans::with('barang')
            ->where('ruangan_id', $ruanganId)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->barang->id,
                    'nama' => $item->barang->nama,
                    'merek' => $item->barang->merek,
                    'foto' => asset('image/barang/' . $item->barang->foto),
                    'stok' => $item->stok,
                ];
            });

        return response()->json($barang);
    }


}
