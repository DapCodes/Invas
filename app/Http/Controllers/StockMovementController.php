<?php

namespace App\Http\Controllers;

use App\Exports\StockMovementExport;
use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\Ruangans;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\StockAdjustmentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

Carbon::setLocale('id');

class StockMovementController extends Controller
{
    protected StockAdjustmentService $adjustmentService;
    protected InventoryService $inventoryService;

    public function __construct(StockAdjustmentService $adjustmentService, InventoryService $inventoryService)
    {
        $this->middleware('auth');
        $this->adjustmentService = $adjustmentService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Central Stock Movement Ledger & Audit Trail
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $barangId = $request->input('barang_id');
        $ruanganId = $request->input('ruangan_id');
        $userId = $request->input('user_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $exportType = $request->input('export');

        $query = StockMovement::with(['barang.unit', 'inventoryItem', 'ruangan', 'user'])
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->when($barangId, function ($q) use ($barangId) {
                $q->where('barang_id', $barangId);
            })
            ->when($ruanganId, function ($q) use ($ruanganId) {
                $q->where('ruangan_id', $ruanganId);
            })
            ->when($userId, function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->when($startDate && !$endDate, function ($q) use ($startDate) {
                $q->where('tanggal', '>=', $startDate . ' 00:00:00');
            })
            ->when(!$startDate && $endDate, function ($q) use ($endDate) {
                $q->where('tanggal', '<=', $endDate . ' 23:59:59');
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('keterangan', 'like', "%{$search}%")
                        ->orWhere('reference_type', 'like', "%{$search}%")
                        ->orWhereHas('barang', function ($b) use ($search) {
                            $b->where('nama', 'like', "%{$search}%")
                              ->orWhere('merek', 'like', "%{$search}%")
                              ->orWhere('kode_barang', 'like', "%{$search}%");
                        })
                        ->orWhereHas('inventoryItem', function ($it) use ($search) {
                            $it->where('serial_number', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('ruangan', function ($r) use ($search) {
                            $r->where('nama_ruangan', 'like', "%{$search}%");
                        });
                });
            });

        // Export
        if ($exportType) {
            $movements = $query->orderBy('id', 'desc')->get();

            if ($exportType === 'excel') {
                return Excel::download(new StockMovementExport($movements), 'buku-mutasi-audit-stok.xlsx');
            } elseif ($exportType === 'pdf') {
                $pdf = Pdf::loadView('pdf.stockMovement', ['movements' => $movements]);
                return $pdf->download('buku-mutasi-audit-stok.pdf');
            }
        }

        $movements = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $barangs = Barangs::orderBy('nama', 'asc')->get();
        $ruangans = Ruangans::orderBy('nama_ruangan', 'asc')->get();
        $users = User::orderBy('name', 'asc')->get();

        return view('stock_movement.index', compact('movements', 'barangs', 'ruangans', 'users', 'type', 'barangId', 'ruanganId', 'userId', 'startDate', 'endDate', 'search'));
    }

    /**
     * Stock Opname & Adjustment Page
     */
    public function adjustmentIndex()
    {
        $barangs = Barangs::with(['unit', 'inventoryItems' => function ($q) {
            $q->whereNotIn('status', ['lost', 'damaged', 'depleted']);
        }, 'barangRuangan.ruangan'])->where('is_active', true)->orderBy('nama', 'asc')->get();

        $ruangans = Ruangans::orderBy('nama_ruangan', 'asc')->get();

        // Recent Adjustments
        $recentAdjustments = StockMovement::with(['barang.unit', 'inventoryItem', 'ruangan', 'user'])
            ->where('type', 'adjustment')
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        return view('stock_movement.adjustment', compact('barangs', 'ruangans', 'recentAdjustments'));
    }

    /**
     * Process Stock Opname Correction
     */
    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'type' => 'required|in:serialized,non_serialized',
            'inventory_item_id' => 'required_if:type,serialized|nullable|exists:inventory_items,id',
            'barang_id' => 'required_if:type,non_serialized|nullable|exists:barangs,id',
            'actual_quantity' => 'required|numeric|min:0',
            'alasan' => 'required|string|max:255',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'new_status' => 'nullable|in:available,in_use,damaged,lost,maintenance,depleted',
        ]);

        try {
            if ($request->type === 'serialized') {
                $item = $this->adjustmentService->adjustSerialized(
                    (int) $request->inventory_item_id,
                    (float) $request->actual_quantity,
                    $request->new_status ?? 'available',
                    $request->alasan,
                    Auth::id()
                );

                Alert::success('Berhasil!', "Koreksi stok unit serial {$item->serial_number} menjadi {$item->current_quantity} {$item->barang?->unit?->symbol} berhasil dicatat.");
            } else {
                $barang = $this->adjustmentService->adjustNonSerialized(
                    (int) $request->barang_id,
                    $request->ruangan_id ? (int) $request->ruangan_id : null,
                    (float) $request->actual_quantity,
                    $request->alasan,
                    Auth::id()
                );

                Alert::success('Berhasil!', "Koreksi stok barang {$barang->nama} berhasil dicatat.");
            }

            return redirect()->back();
        } catch (Exception $e) {
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Dedicated Report Center Hub
     */
    public function reportsHub(Request $request)
    {
        $barangs = Barangs::orderBy('nama', 'asc')->get();
        $ruangans = Ruangans::orderBy('nama_ruangan', 'asc')->get();
        return view('reports.index', compact('barangs', 'ruangans'));
    }
}
