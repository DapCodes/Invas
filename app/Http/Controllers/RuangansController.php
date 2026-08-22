<?php

namespace App\Http\Controllers;

use App\Exports\RuanganExport;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\LocationHistory;
use App\Models\Ruangans;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class RuangansController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of rooms
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $exportType = $request->input('export');

        $ruanganQuery = Ruangans::withCount(['barangRuangan', 'inventoryItems']);

        if ($keyword) {
            $ruanganQuery->where(function ($query) use ($keyword) {
                $query->where('nama_ruangan', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%");
            });
        }

        if ($exportType) {
            $ruangan = $ruanganQuery->get();

            if ($exportType == 'excel') {
                return Excel::download(new RuanganExport($ruangan), 'laporan-data-ruangan.xlsx');
            }

            if ($exportType == 'pdf') {
                $pdf = Pdf::loadView('pdf.ruangan', ['ruangan' => $ruangan]);
                return $pdf->download('laporan-data-ruangan.pdf');
            }
        }

        $ruangan = $ruanganQuery->orderBy('nama_ruangan', 'asc')->paginate(10)->withQueryString();

        return view('ruangan.index', compact('ruangan', 'keyword'));
    }

    /**
     * Store new room
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        Ruangans::create([
            'nama_ruangan' => $request->nama_ruangan,
            'deskripsi' => $request->deskripsi,
        ]);

        Alert::success('Berhasil!', 'Data ruangan berhasil disimpan.');
        return redirect()->route('ruangan.index');
    }

    /**
     * Display detail of room with its stocks and serials
     */
    public function show($id)
    {
        $ruangan = Ruangans::withCount(['barangRuangan', 'inventoryItems'])->findOrFail($id);

        $nonSerialStocks = BarangRuangans::with(['barang.unit'])
            ->where('ruangan_id', $id)
            ->where('stok', '>', 0)
            ->get();

        $serialUnits = InventoryItem::with(['barang.unit'])
            ->where('ruangan_id', $id)
            ->whereNotIn('status', ['lost', 'damaged', 'depleted'])
            ->get();

        $recentTransfers = LocationHistory::with(['inventoryItem.barang', 'fromRuangan', 'toRuangan', 'user'])
            ->where(function ($q) use ($id) {
                $q->where('from_ruangan_id', $id)->orWhere('to_ruangan_id', $id);
            })
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        return view('ruangan.show', compact('ruangan', 'nonSerialStocks', 'serialUnits', 'recentTransfers'));
    }

    /**
     * Update room
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $ruangan = Ruangans::findOrFail($id);
        $ruangan->update($validated);

        Alert::success('Berhasil!', 'Data ruangan berhasil diperbarui.');
        return redirect()->route('ruangan.index');
    }

    /**
     * Destroy room
     */
    public function destroy($id)
    {
        $ruangan = Ruangans::findOrFail($id);

        // Check if room still has items
        $hasItems = BarangRuangans::where('ruangan_id', $id)->where('stok', '>', 0)->exists()
            || InventoryItem::where('ruangan_id', $id)->whereNotIn('status', ['lost', 'damaged', 'depleted'])->exists();

        if ($hasItems) {
            Alert::error('Gagal!', 'Ruangan tidak dapat dihapus karena masih terdapat stok barang / unit serial di dalamnya.');
            return redirect()->route('ruangan.index');
        }

        $ruangan->delete();
        Alert::success('Dihapus!', 'Data Ruangan Berhasil Dihapus');
        return redirect()->route('ruangan.index');
    }
}
