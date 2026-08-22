<?php

namespace App\Http\Controllers;

use App\Exports\BarangKeluarExport;
use App\Http\Requests\StoreBarangKeluarRequest;
use App\Models\Barangs;
use App\Models\BarangKeluars;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\Ruangans;
use App\Services\InventoryService;
use App\Services\StockMovementService;
use App\Services\StockOutService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

Carbon::setLocale('id');

class BarangKeluarController extends Controller
{
    protected StockOutService $stockOutService;
    protected InventoryService $inventoryService;
    protected StockMovementService $movementService;

    public function __construct(
        StockOutService $stockOutService,
        InventoryService $inventoryService,
        StockMovementService $movementService
    ) {
        $this->middleware('auth');
        $this->stockOutService = $stockOutService;
        $this->inventoryService = $inventoryService;
        $this->movementService = $movementService;
    }

    /**
     * Display listing of outgoing stock transactions
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $exportType = $request->input('export');

        $query = BarangKeluars::with(['barang.unit', 'inventoryItem', 'ruangan', 'user'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('kode_barang', 'like', "%{$keyword}%")
                        ->orWhere('keterangan', 'like', "%{$keyword}%")
                        ->orWhereHas('barang', function ($q) use ($keyword) {
                            $q->where('nama', 'like', "%{$keyword}%")
                              ->orWhere('merek', 'like', "%{$keyword}%")
                              ->orWhere('kode_barang', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('inventoryItem', function ($q) use ($keyword) {
                            $q->where('serial_number', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('ruangan', function ($q) use ($keyword) {
                            $q->where('nama_ruangan', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('user', function ($q) use ($keyword) {
                            $q->where('name', 'like', "%{$keyword}%");
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
        if ($exportType) {
            $barangKeluarForExport = $query->orderBy('id', 'desc')->get();

            if ($exportType === 'excel') {
                return Excel::download(new BarangKeluarExport($barangKeluarForExport), 'laporan-data-barangkeluar.xlsx');
            }

            if ($exportType === 'pdf') {
                $pdf = Pdf::loadView('pdf.barangKeluar', ['barangKeluar' => $barangKeluarForExport]);
                return $pdf->download('laporan-data-barangkeluar.pdf');
            }
        }

        $barangKeluar = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('barangkeluar.index', compact('barangKeluar', 'keyword', 'startDate', 'endDate'));
    }

    /**
     * Show form for creating outgoing stock
     */
    public function create()
    {
        $barang = Barangs::with(['unit', 'inventoryItems' => function ($q) {
            $q->where('status', 'available')->where('current_quantity', '>', 0);
        }])->where('is_active', true)->where('stok', '>', 0)->orderBy('nama', 'asc')->get();

        $ruangan = Ruangans::orderBy('nama_ruangan', 'asc')->get();

        return view('barangkeluar.create', compact('barang', 'ruangan'));
    }

    /**
     * Store outgoing stock transaction
     */
    public function store(StoreBarangKeluarRequest $request)
    {
        try {
            $barang = Barangs::findOrFail($request->id_barang);

            if ($barang->has_serial_number) {
                $itemId = (int) $request->inventory_item_id;
                $quantity = (float) $request->jumlah;
                $meta = [
                    'tanggal_keluar' => $request->tanggal_keluar,
                    'keterangan' => $request->keterangan,
                    'ruangan_id' => $request->ruangan_id,
                ];

                $barangKeluar = $this->stockOutService->processSerialized($itemId, $quantity, $meta, Auth::id());
                Alert::success('Berhasil!', "Pengeluaran unit serial {$barangKeluar->inventoryItem?->serial_number} sebesar {$quantity} {$barang->unit?->symbol} berhasil dicatat.");
            } else {
                $this->stockOutService->processNonSerialized([
                    'barang_id' => $barang->id,
                    'jumlah' => $request->jumlah,
                    'ruangan_id' => $request->ruangan_id,
                    'tanggal_keluar' => $request->tanggal_keluar,
                    'keterangan' => $request->keterangan,
                ], Auth::id());

                Alert::success('Berhasil!', 'Pengeluaran barang non-serial berhasil dicatat.');
            }

            return redirect()->route('brg-keluar.index');
        } catch (Exception $e) {
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Display detailed transaction
     */
    public function show($id)
    {
        $barangKeluar = BarangKeluars::with(['barang.unit', 'inventoryItem', 'ruangan', 'user'])->findOrFail($id);
        return view('barangkeluar.show', compact('barangKeluar'));
    }

    /**
     * Remove / void outgoing stock transaction safely
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $barangKeluar = BarangKeluars::lockForUpdate()->findOrFail($id);
            $barang = Barangs::lockForUpdate()->findOrFail($barangKeluar->id_barang);
            $qtyToRestore = (float) $barangKeluar->jumlah;

            if ($barangKeluar->inventory_item_id) {
                $item = InventoryItem::lockForUpdate()->find($barangKeluar->inventory_item_id);
                if ($item) {
                    $itemQtyBefore = (float) $item->current_quantity;
                    $itemQtyAfter = $itemQtyBefore + $qtyToRestore;
                    $item->current_quantity = $itemQtyAfter;
                    if ($item->status === 'out' || $item->status === 'depleted') {
                        $item->status = 'available';
                    }
                    $item->save();

                    // Movement reversal
                    $this->movementService->record(
                        $barang->id,
                        $item->id,
                        'adjustment',
                        $qtyToRestore,
                        $itemQtyBefore,
                        $itemQtyAfter,
                        'barang_keluar_void',
                        $barangKeluar->id,
                        $barangKeluar->ruangan_id,
                        Auth::id(),
                        "Pembatalan Barang Keluar #{$barangKeluar->kode_barang}"
                    );
                }
            } else {
                $qtyBefore = (float) $barang->stok;
                $qtyAfter = $qtyBefore + $qtyToRestore;
                $barang->stok = $qtyAfter;
                $barang->save();

                if ($barangKeluar->ruangan_id) {
                    $room = BarangRuangans::lockForUpdate()->firstOrNew([
                        'barang_id' => $barang->id,
                        'ruangan_id' => $barangKeluar->ruangan_id,
                    ]);
                    $room->stok = (float) ($room->stok ?? 0) + $qtyToRestore;
                    $room->save();
                }

                $this->movementService->record(
                    $barang->id,
                    null,
                    'adjustment',
                    $qtyToRestore,
                    $qtyBefore,
                    $qtyAfter,
                    'barang_keluar_void',
                    $barangKeluar->id,
                    $barangKeluar->ruangan_id,
                    Auth::id(),
                    "Pembatalan Barang Keluar #{$barangKeluar->kode_barang}"
                );
            }

            $barangKeluar->delete();
            $this->inventoryService->syncMasterStock($barang->id);

            DB::commit();

            Alert::success('Berhasil!', 'Data barang keluar berhasil dibatalkan dan stok dikembalikan.');
            return redirect()->route('brg-keluar.index');
        } catch (Exception $e) {
            DB::rollBack();
            Alert::error('Gagal!', $e->getMessage());
            return redirect()->route('brg-keluar.index');
        }
    }

    /**
     * API to get available rooms or serials for a barang
     */
    public function getBarangByRuangan($ruanganId)
    {
        $barang = BarangRuangans::with('barang.unit')
            ->where('ruangan_id', $ruanganId)
            ->where('stok', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->barang->id,
                    'nama' => $item->barang->nama,
                    'merek' => $item->barang->merek,
                    'foto' => asset('image/barang/' . $item->barang->foto),
                    'stok' => (float) $item->stok,
                    'satuan' => $item->barang->unit?->symbol ?? 'pcs',
                ];
            });

        return response()->json($barang);
    }
}
