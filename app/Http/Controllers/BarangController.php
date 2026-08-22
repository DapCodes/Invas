<?php

namespace App\Http\Controllers;

use App\Exports\BarangExport;
use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;
use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\Peminjamans;
use App\Models\Ruangans;
use App\Models\Unit;
use App\Models\Vendor;
use App\Services\InventoryService;
use App\Services\StockInService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

Carbon::setLocale('id');

class BarangController extends Controller
{
    protected InventoryService $inventoryService;
    protected StockInService $stockInService;

    public function __construct(InventoryService $inventoryService, StockInService $stockInService)
    {
        $this->middleware('auth');
        $this->inventoryService = $inventoryService;
        $this->stockInService = $stockInService;
    }

    /**
     * Display listing of barangs with filters
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $type = $request->input('type'); // 'serialized', 'non_serialized'
        $vendorId = $request->input('vendor_id');
        $unitId = $request->input('satuan_id');
        $exportType = $request->input('export');

        $barangQuery = Barangs::with(['user', 'vendor', 'unit'])
            ->withCount('inventoryItems')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%")
                      ->orWhere('merek', 'like', "%{$keyword}%")
                      ->orWhere('kode_barang', 'like', "%{$keyword}%")
                      ->orWhereHas('inventoryItems', function ($sub) use ($keyword) {
                          $sub->where('serial_number', 'like', "%{$keyword}%");
                      });
                })->orWhereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                })->orWhereHas('vendor', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->when($type === 'serialized', function ($q) {
                $q->where('has_serial_number', true);
            })
            ->when($type === 'non_serialized', function ($q) {
                $q->where('has_serial_number', false);
            })
            ->when($vendorId, function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->when($unitId, function ($q) use ($unitId) {
                $q->where('satuan_id', $unitId);
            });

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

        $barang = $barangQuery->orderBy('nama', 'asc')->paginate(10)->withQueryString();
        $vendors = Vendor::orderBy('name', 'asc')->get();
        $units = Unit::orderBy('name', 'asc')->get();

        return view('barang.index', compact('barang', 'vendors', 'units', 'keyword', 'type', 'vendorId', 'unitId'));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('name', 'asc')->get();
        $units = Unit::orderBy('name', 'asc')->get();
        $ruangans = Ruangans::orderBy('nama_ruangan', 'asc')->get();

        return view('barang.create', compact('vendors', 'units', 'ruangans'));
    }

    /**
     * Store new master item (supports both Serialized and Non-Serialized modes)
     */
    public function store(StoreBarangRequest $request)
    {
        try {
            DB::beginTransaction();

            $barang = new Barangs();

            $lastRecord = Barangs::latest('id')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $kodeBarang = 'B-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

            $hasSerial = $request->boolean('has_serial_number');

            $barang->kode_barang = $kodeBarang;
            $barang->nama = $request->nama;
            $barang->merek = $request->merek;
            $barang->satuan_id = $request->satuan_id;
            $barang->vendor_id = $request->vendor_id ?: null;
            $barang->deskripsi = $request->deskripsi;
            $barang->has_serial_number = $hasSerial;
            $barang->is_active = true;
            $barang->stok = 0;
            $barang->id_user = Auth::id();

            if ($request->hasFile('foto')) {
                $img = $request->file('foto');
                $name = rand(1000, 9999) . '_' . time() . '.' . $img->getClientOriginalExtension();
                $img->move('image/barang', $name);
                $barang->foto = $name;
            }

            $barang->save();

            // Handle initial stock based on type
            if ($hasSerial) {
                // If serial rows provided
                $serialRows = $request->input('serials', []);
                if (!empty($serialRows)) {
                    $meta = [
                        'tanggal_masuk' => Carbon::now()->toDateString(),
                        'keterangan' => 'Penerimaan stok awal master barang',
                        'ruangan_id' => $request->ruangan_id,
                    ];
                    $this->stockInService->processSerialized($barang->id, $serialRows, $meta, Auth::id());
                }
            } else {
                // Non-serialized initial stock
                $initialStock = (float) $request->input('stok', 0);
                if ($initialStock > 0) {
                    $this->stockInService->processNonSerialized([
                        'barang_id' => $barang->id,
                        'jumlah' => $initialStock,
                        'ruangan_id' => $request->ruangan_id,
                        'tanggal_masuk' => Carbon::now()->toDateString(),
                        'keterangan' => 'Stok awal barang',
                    ], Auth::id());
                }
            }

            DB::commit();

            Alert::success('Berhasil!', 'Data Master Barang Berhasil Ditambahkan');
            return redirect()->route('barang.show', $barang->id);
        } catch (Exception $e) {
            DB::rollBack();
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Show detail master item + dashboard summary + inventory items / stock movements
     */
    public function show($id)
    {
        $barang = Barangs::with(['vendor', 'unit', 'user'])->findOrFail($id);
        $summary = $this->inventoryService->getStockSummary($barang);

        $inventoryItems = [];
        if ($barang->has_serial_number) {
            $inventoryItems = InventoryItem::with(['ruangan', 'unit'])
                ->where('barang_id', $barang->id)
                ->orderBy('status', 'asc')
                ->orderBy('serial_number', 'asc')
                ->paginate(15, ['*'], 'serial_page')
                ->withQueryString();
        }

        $stockMovements = $barang->stockMovements()
            ->with(['inventoryItem', 'ruangan', 'user'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'movement_page')
            ->withQueryString();

        $roomStocks = BarangRuangans::with('ruangan')
            ->where('barang_id', $barang->id)
            ->where('stok', '>', 0)
            ->get();

        $ruangans = Ruangans::orderBy('nama_ruangan', 'asc')->get();

        return view('barang.show', compact('barang', 'summary', 'inventoryItems', 'stockMovements', 'roomStocks', 'ruangans'));
    }

    public function edit($id)
    {
        $barang = Barangs::with(['unit', 'vendor'])->findOrFail($id);
        $vendors = Vendor::orderBy('name', 'asc')->get();
        $units = Unit::orderBy('name', 'asc')->get();

        return view('barang.edit', compact('barang', 'vendors', 'units'));
    }

    public function update(UpdateBarangRequest $request, $id)
    {
        try {
            $barang = Barangs::findOrFail($id);

            $barang->nama = $request->nama;
            $barang->merek = $request->merek;
            $barang->satuan_id = $request->satuan_id;
            $barang->vendor_id = $request->vendor_id ?: null;
            $barang->deskripsi = $request->deskripsi;
            if ($request->has('is_active')) {
                $barang->is_active = $request->boolean('is_active');
            }

            if ($request->hasFile('foto')) {
                if ($barang->foto && file_exists(public_path('image/barang/' . $barang->foto))) {
                    unlink(public_path('image/barang/' . $barang->foto));
                }

                $img = $request->file('foto');
                $name = rand(1000, 9999) . '_' . time() . '.' . $img->getClientOriginalExtension();
                $img->move('image/barang', $name);
                $barang->foto = $name;
            }

            $barang->save();

            // Refresh cached stock
            $this->inventoryService->syncMasterStock($barang->id);

            Alert::success('Berhasil!', 'Data Master Barang Berhasil Diperbarui');
            return redirect()->route('barang.show', $barang->id);
        } catch (Exception $e) {
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        $barang = Barangs::findOrFail($id);

        $pinjaman = Peminjamans::where('id_barang', $barang->id)->where('status', 'Sedang Dipinjam')->count();
        if ($pinjaman > 0) {
            Alert::warning('Gagal!', 'Data tidak dapat dihapus karena masih ada unit yang sedang dipinjam!');
            return redirect()->route('barang.index');
        }

        // Check transactions
        $hasTransactions = $barang->stockMovements()->count() > 1 || $barang->barangmasuk()->count() > 0;
        if ($hasTransactions) {
            $barang->is_active = false;
            $barang->save();

            Alert::info('Info', 'Barang memiliki riwayat transaksi, barang ditandai sebagai Nonaktif (Inactive).');
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
