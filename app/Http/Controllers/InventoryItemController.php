<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryItemRequest;
use App\Http\Requests\UpdateInventoryItemRequest;
use App\Models\Barangs;
use App\Models\InventoryItem;
use App\Models\Ruangans;
use App\Services\InventoryService;
use App\Services\StockAdjustmentService;
use App\Services\StockInService;
use App\Services\TransferService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class InventoryItemController extends Controller
{
    protected InventoryService $inventoryService;
    protected StockInService $stockInService;
    protected TransferService $transferService;
    protected StockAdjustmentService $adjustmentService;

    public function __construct(
        InventoryService $inventoryService,
        StockInService $stockInService,
        TransferService $transferService,
        StockAdjustmentService $adjustmentService
    ) {
        $this->middleware('auth');
        $this->inventoryService = $inventoryService;
        $this->stockInService = $stockInService;
        $this->transferService = $transferService;
        $this->adjustmentService = $adjustmentService;
    }

    /**
     * Display listing of inventory items
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $status = $request->input('status');
        $ruanganId = $request->input('ruangan_id');
        $barangId = $request->input('barang_id');

        $query = InventoryItem::with(['barang.unit', 'ruangan', 'user'])
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('serial_number', 'like', "%{$keyword}%")
                        ->orWhereHas('barang', function ($b) use ($keyword) {
                            $b->where('nama', 'like', "%{$keyword}%")
                              ->orWhere('kode_barang', 'like', "%{$keyword}%")
                              ->orWhere('merek', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($ruanganId, function ($q) use ($ruanganId) {
                $q->where('ruangan_id', $ruanganId);
            })
            ->when($barangId, function ($q) use ($barangId) {
                $q->where('barang_id', $barangId);
            });

        $items = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $ruangans = Ruangans::orderBy('nama_ruangan', 'asc')->get();
        $barangs = Barangs::where('has_serial_number', true)->orderBy('nama', 'asc')->get();

        return view('inventory_item.index', compact('items', 'ruangans', 'barangs', 'keyword', 'status', 'ruanganId', 'barangId'));
    }

    /**
     * Show create form for a unit/serial
     */
    public function create(Request $request)
    {
        $barangId = $request->input('barang_id');
        $barang = $barangId ? Barangs::with('unit')->findOrFail($barangId) : null;
        $barangs = Barangs::where('has_serial_number', true)->orderBy('nama', 'asc')->get();
        $ruangans = Ruangans::orderBy('nama_ruangan', 'asc')->get();

        return view('inventory_item.create', compact('barang', 'barangs', 'ruangans'));
    }

    /**
     * Store new serial item
     */
    public function store(StoreInventoryItemRequest $request)
    {
        try {
            $barangId = (int) $request->barang_id;
            $serialRows = [
                [
                    'serial_number' => $request->serial_number,
                    'quantity' => $request->initial_quantity ?: 1.0,
                    'ruangan_id' => $request->ruangan_id,
                ]
            ];
            $metaData = [
                'tanggal_masuk' => $request->tanggal_masuk,
                'keterangan' => $request->keterangan ?? 'Penambahan unit serial baru',
                'ruangan_id' => $request->ruangan_id,
            ];

            $this->stockInService->processSerialized($barangId, $serialRows, $metaData, Auth::id());

            Alert::success('Berhasil!', "Unit Serial {$request->serial_number} berhasil ditambahkan.");
            return redirect()->route('barang.show', $barangId);
        } catch (Exception $e) {
            Alert::error('Gagal!', $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Show detailed page for a serial item
     */
    public function show($id)
    {
        $item = InventoryItem::with([
            'barang.unit',
            'barang.vendor',
            'ruangan',
            'user',
            'stockMovements.user',
            'stockMovements.ruangan',
            'locationHistories.fromRuangan',
            'locationHistories.toRuangan',
            'locationHistories.user',
        ])->findOrFail($id);

        $ruangans = Ruangans::orderBy('nama_ruangan', 'asc')->get();

        return view('inventory_item.show', compact('item', 'ruangans'));
    }

    /**
     * Show edit form for serial item
     */
    public function edit($id)
    {
        $item = InventoryItem::with(['barang.unit', 'ruangan'])->findOrFail($id);
        $ruangans = Ruangans::orderBy('nama_ruangan', 'asc')->get();

        return view('inventory_item.edit', compact('item', 'ruangans'));
    }

    /**
     * Update serial item info
     */
    public function update(UpdateInventoryItemRequest $request, $id)
    {
        try {
            $item = InventoryItem::findOrFail($id);

            // Check if location changed
            if ($request->ruangan_id && $request->ruangan_id != $item->ruangan_id) {
                $this->transferService->transferSerialized(
                    $item->id,
                    (int) $request->ruangan_id,
                    'Perubahan lokasi dari form edit unit',
                    Auth::id()
                );
            }

            $item->serial_number = $request->serial_number;
            $item->status = $request->status;
            $item->keterangan = $request->keterangan;
            $item->save();

            $this->inventoryService->syncMasterStock($item->barang_id);

            Alert::success('Berhasil!', 'Data unit serial berhasil diperbarui.');
            return redirect()->route('inventory-item.show', $item->id);
        } catch (Exception $e) {
            Alert::error('Gagal!', $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Quick Transfer Location
     */
    public function transfer(Request $request, $id)
    {
        $request->validate([
            'to_ruangan_id' => 'required|exists:ruangans,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            $this->transferService->transferSerialized(
                (int) $id,
                (int) $request->to_ruangan_id,
                $request->keterangan,
                Auth::id()
            );

            Alert::success('Berhasil!', 'Lokasi unit serial berhasil dipindahkan.');
            return redirect()->back();
        } catch (Exception $e) {
            Alert::error('Gagal!', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Quick Stock Adjustment
     */
    public function adjust(Request $request, $id)
    {
        $request->validate([
            'new_quantity' => 'required|numeric|min:0',
            'new_status' => 'nullable|in:available,borrowed,in_use,out,damaged,lost,maintenance,depleted',
            'alasan' => 'required|string|max:255',
        ]);

        try {
            $this->adjustmentService->adjustSerialized(
                (int) $id,
                (float) $request->new_quantity,
                $request->new_status,
                $request->alasan,
                Auth::id()
            );

            Alert::success('Berhasil!', 'Stok unit serial berhasil disesuaikan.');
            return redirect()->back();
        } catch (Exception $e) {
            Alert::error('Gagal!', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Remove or mark inactive
     */
    public function destroy($id)
    {
        $item = InventoryItem::findOrFail($id);

        if ($item->status === 'borrowed') {
            Alert::warning('Gagal!', 'Unit serial ini sedang dalam masa peminjaman!');
            return redirect()->back();
        }

        // Check movements
        $hasMovements = $item->stockMovements()->count() > 1;
        if ($hasMovements) {
            $item->status = 'depleted';
            $item->current_quantity = 0;
            $item->save();
            $this->inventoryService->syncMasterStock($item->barang_id);

            Alert::info('Info', 'Unit serial memiliki riwayat transaksi, status diubah menjadi habis/nonaktif.');
            return redirect()->back();
        }

        $barangId = $item->barang_id;
        $item->delete();
        $this->inventoryService->syncMasterStock($barangId);

        Alert::success('Berhasil!', 'Unit serial berhasil dihapus.');
        return redirect()->route('barang.show', $barangId);
    }
}
