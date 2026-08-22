<?php

namespace App\Http\Controllers;

use App\Exports\BarangMasukExport;
use App\Http\Requests\StoreBarangMasukRequest;
use App\Models\Barangs;
use App\Models\BarangMasuks;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\Ruangans;
use App\Services\InventoryService;
use App\Services\StockInService;
use App\Services\StockMovementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

Carbon::setLocale('id');

class BarangMasukController extends Controller
{
    protected StockInService $stockInService;
    protected InventoryService $inventoryService;
    protected StockMovementService $movementService;

    public function __construct(
        StockInService $stockInService,
        InventoryService $inventoryService,
        StockMovementService $movementService
    ) {
        $this->middleware('auth');
        $this->stockInService = $stockInService;
        $this->inventoryService = $inventoryService;
        $this->movementService = $movementService;
    }

    /**
     * Display listing of incoming stock transactions
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $exportType = $request->input('export');

        $query = BarangMasuks::with(['barang.unit', 'inventoryItem', 'ruangan', 'user'])
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
                $query->whereBetween('tanggal_masuk', [$startDate, $endDate]);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                $query->whereDate('tanggal_masuk', '>=', $startDate);
            })
            ->when(!$startDate && $endDate, function ($query) use ($endDate) {
                $query->whereDate('tanggal_masuk', '<=', $endDate);
            });

        // Ekspor jika diminta
        if ($exportType) {
            $barangMasukForExport = $query->orderBy('id', 'desc')->get();

            if ($exportType === 'excel') {
                return Excel::download(new BarangMasukExport($barangMasukForExport), 'laporan-data-barangMasuk.xlsx');
            }

            if ($exportType === 'pdf') {
                $pdf = Pdf::loadView('pdf.barangMasuk', ['barangMasuk' => $barangMasukForExport]);
                return $pdf->download('laporan-data-barangMasuk.pdf');
            }
        }

        $barangMasuk = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('barangmasuk.index', compact('barangMasuk', 'keyword', 'startDate', 'endDate'));
    }

    /**
     * Show form for creating incoming stock
     */
    public function create()
    {
        $barang = Barangs::with(['unit', 'inventoryItems' => function ($q) {
            $q->whereNotIn('status', ['lost', 'damaged']);
        }])->where('is_active', true)->orderBy('nama', 'asc')->get();

        $ruangan = Ruangans::orderBy('nama_ruangan', 'asc')->get();

        return view('barangmasuk.create', compact('barang', 'ruangan'));
    }

    /**
     * Store incoming stock
     */
    public function store(StoreBarangMasukRequest $request)
    {
        try {
            $barang = Barangs::findOrFail($request->id_barang);

            if ($barang->has_serial_number) {
                // Serialized incoming stock (supports multiple serial rows + existing serial top-up)
                $serialRows = $request->input('serials', []);
                $meta = [
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'keterangan' => $request->keterangan,
                    'ruangan_id' => $request->ruangan_id,
                ];

                $this->stockInService->processSerialized($barang->id, $serialRows, $meta, Auth::id());

                Alert::success('Berhasil!', 'Penerimaan barang serial (' . count($serialRows) . ' unit) berhasil dicatat.');
            } else {
                // Non-serialized incoming stock
                $this->stockInService->processNonSerialized([
                    'barang_id' => $barang->id,
                    'jumlah' => $request->jumlah,
                    'ruangan_id' => $request->ruangan_id,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'keterangan' => $request->keterangan,
                    'satuan_id' => $barang->satuan_id,
                ], Auth::id());

                Alert::success('Berhasil!', 'Penerimaan barang non-serial berhasil dicatat.');
            }

            return redirect()->route('brg-masuk.index');
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
        $barangMasuk = BarangMasuks::with(['barang.unit', 'inventoryItem', 'ruangan', 'user'])->findOrFail($id);
        return view('barangmasuk.show', compact('barangMasuk'));
    }

    /**
     * Remove / void incoming stock transaction safely
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $barangMasuk = BarangMasuks::lockForUpdate()->findOrFail($id);
            $barang = Barangs::lockForUpdate()->findOrFail($barangMasuk->id_barang);
            $qtyToRevert = (float) $barangMasuk->jumlah;

            if ($barangMasuk->inventory_item_id) {
                $item = InventoryItem::lockForUpdate()->find($barangMasuk->inventory_item_id);
                if ($item) {
                    if ($item->status === 'borrowed') {
                        throw new Exception('Unit serial terkait transaksi ini sedang dipinjam.');
                    }
                    $itemQtyBefore = (float) $item->current_quantity;
                    if ($itemQtyBefore < $qtyToRevert) {
                        throw new Exception('Sisa kuantitas pada unit serial tidak mencukupi untuk pembatalan transaksi.');
                    }

                    $itemQtyAfter = $itemQtyBefore - $qtyToRevert;
                    $item->current_quantity = $itemQtyAfter;
                    if ($itemQtyAfter <= 0) {
                        $item->status = 'depleted';
                    }
                    $item->save();

                    // Movement reversal
                    $this->movementService->record(
                        $barang->id,
                        $item->id,
                        'adjustment',
                        -$qtyToRevert,
                        $itemQtyBefore,
                        $itemQtyAfter,
                        'barang_masuk_void',
                        $barangMasuk->id,
                        $barangMasuk->ruangan_id,
                        Auth::id(),
                        "Pembatalan Barang Masuk #{$barangMasuk->kode_barang}"
                    );
                }
            } else {
                if ((float) $barang->stok < $qtyToRevert) {
                    throw new Exception('Stok barang saat ini kurang dari jumlah yang ingin dibatalkan.');
                }

                $qtyBefore = (float) $barang->stok;
                $qtyAfter = $qtyBefore - $qtyToRevert;
                $barang->stok = $qtyAfter;
                $barang->save();

                if ($barangMasuk->ruangan_id) {
                    $room = BarangRuangans::lockForUpdate()->where('barang_id', $barang->id)
                        ->where('ruangan_id', $barangMasuk->ruangan_id)
                        ->first();
                    if ($room) {
                        $room->stok = max(0, (float) $room->stok - $qtyToRevert);
                        $room->save();
                    }
                }

                $this->movementService->record(
                    $barang->id,
                    null,
                    'adjustment',
                    -$qtyToRevert,
                    $qtyBefore,
                    $qtyAfter,
                    'barang_masuk_void',
                    $barangMasuk->id,
                    $barangMasuk->ruangan_id,
                    Auth::id(),
                    "Pembatalan Barang Masuk #{$barangMasuk->kode_barang}"
                );
            }

            $barangMasuk->delete();
            $this->inventoryService->syncMasterStock($barang->id);

            DB::commit();

            Alert::success('Berhasil!', 'Data barang masuk berhasil dibatalkan dan stok disesuaikan.');
            return redirect()->route('brg-masuk.index');
        } catch (Exception $e) {
            DB::rollBack();
            Alert::error('Gagal!', $e->getMessage());
            return redirect()->route('brg-masuk.index');
        }
    }
}