<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Barangs;
use App\Models\Vendor;
use App\Models\Peminjamans;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BarangExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

Carbon::setLocale('id');

class BarangController extends Controller
{
    // cek auth
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $exportType = $request->input('export');

        $barangQuery = Barangs::with(['user', 'vendor']);

        // Filter pencarian
        if ($keyword) {
            $barangQuery->where(function ($query) use ($keyword) {
                $query->where('nama', 'like', "%$keyword%")
                    ->orWhere('merek', 'like', "%$keyword%")
                    ->orWhere('kode_barang', 'like', "%$keyword%")
                    ->orWhere('serial_number', 'like', "%$keyword%");
            })->orWhereHas('user', function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            })->orWhereHas('vendor', function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            });
        }

        // Ekspor data
        if ($exportType) {
            $barang = $barangQuery->get();

            if ($exportType == 'excel') {
                return Excel::download(new BarangExport($barang), 'laporan-data-barang.xlsx');
            }

            if ($exportType == 'pdf') {
                $pdf = Pdf::loadView('pdf.barang', ['barang' => $barang]);
                return $pdf->download('laporan-data-barang.pdf');
            }
        }

        $barang = $barangQuery->orderBy('nama')->paginate(10);

        return view('barang.index', compact('barang', 'keyword'));
    }

    public function create()
    {
        $barang = Barangs::all();
        $vendors = Vendor::orderBy('name', 'asc')->get();

        return view('barang.create', compact('barang', 'vendors'));
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
            'nama' => 'required',
            'merek' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'vendor_id' => 'nullable|exists:vendors,id',
            'serial_number' => 'nullable|string|max:255',
        ],
        [
            'nama.required' => 'Nama Barang tidak boleh kosong',
            'merek.required' => 'Merek Barang tidak boleh kosong',
            'foto.image' => 'File yang diupload harus berupa gambar',
            'foto.mimes' => 'File yang diupload harus berupa jpeg, png, jpg, gif, webp',
            'foto.max' => 'Ukuran file tidak boleh lebih dari 2MB',
            'vendor_id.exists' => 'Vendor yang dipilih tidak valid',
        ]);

        $barang = new Barangs;

        $lastRecord = Barangs::latest('id')->first();   
        $lastId = $lastRecord ? $lastRecord->id : 0;
        $kodeBarang = 'B-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        $barang->kode_barang = $kodeBarang;
        $barang->nama = $request->nama;
        $barang->merek = $request->merek;
        $barang->vendor_id = $request->vendor_id ?: null;
        $barang->serial_number = $request->serial_number ?: null;

        if ($request->hasFile('foto')) {
            $img = $request->file('foto');
            $name = rand(1000,9999) . $img->getClientOriginalName();
            $img->move('image/barang', $name);
            $barang->foto = $name;
        } else {
            $barang->foto = null;
        }

        $userId = Auth::user();
        $barang->id_user = $userId->id;

        $barang->save(); 

        Alert::success('Berhasil!', 'Data Berhasil Ditambahkan');
        return redirect()->route('barang.index');
    }

    public function show($id)
    {
        $barang = Barangs::with('vendor')->findOrFail($id);
        return view('barang.show', compact('barang'));
    }

    public function edit($id)
    {
        $barang = Barangs::findOrFail($id);
        $vendors = Vendor::orderBy('name', 'asc')->get();

        return view('barang.edit', compact('barang', 'vendors'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'merek' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'vendor_id' => 'nullable|exists:vendors,id',
            'serial_number' => 'nullable|string|max:255',
        ],
        [
            'nama.required' => 'Nama Barang tidak boleh kosong',
            'merek.required' => 'Merek Barang tidak boleh kosong',
            'foto.image' => 'File yang diupload harus berupa gambar',
            'foto.mimes' => 'File yang diupload harus berupa jpeg, png, jpg, gif, webp',
            'foto.max' => 'Ukuran file tidak boleh lebih dari 2MB',
            'vendor_id.exists' => 'Vendor yang dipilih tidak valid',
        ]);

        $barang = Barangs::findOrFail($id);
        if ($request->has('kode_barang')) {
            $barang->kode_barang = $request->kode_barang;
        }
        $barang->nama = $request->nama;
        $barang->merek = $request->merek;
        $barang->vendor_id = $request->vendor_id ?: null;
        $barang->serial_number = $request->serial_number ?: null;

        if ($request->hasFile('foto')) {
            if ($barang->foto && file_exists(public_path('image/barang/' . $barang->foto))) {
                unlink(public_path('image/barang/' . $barang->foto));
            }

            $img = $request->file('foto');
            $name = rand(1000,9999) . $img->getClientOriginalName();
            $img->move('image/barang', $name);
            $barang->foto = $name;
        }

        $barang->save(); 

        Alert::success('Berhasil!', 'Data Berhasil Diubah');
        return redirect()->route('barang.index');
    }

    public function destroy($id)
    {
        $barang = Barangs::findOrFail($id);
        $pinjaman = Peminjamans::where('id_barang', $barang->id)->where('status', 'Sedang Dipinjam')->get();

        if (count($pinjaman) > 0) {
            Alert::warning('Gagal!', 'Data tidak dihapus. Karena beberapa stok sedang dipinjam!');
            return redirect()->route('barang.index');
        }

        if ($barang->foto && file_exists(public_path('image/barang/' . $barang->foto))) {
            unlink(public_path('image/barang/' . $barang->foto));
        }

        $barang->delete();
        Alert::success('Dihapus!', 'Data Berhasil Dihapus');
        return redirect()->route('barang.index');
    }
}
