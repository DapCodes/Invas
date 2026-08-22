<?php

namespace App\Http\Controllers;

use App\Exports\PeminjamanExport;
use App\Http\Requests\StorePeminjamanRequest;
use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\Peminjamans;
use App\Models\Pengembalians;
use App\Models\Ruangans;
use App\Services\BorrowingService;
use App\Services\InventoryService;
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

class PeminjamanController extends Controller
{
    protected BorrowingService $borrowingService;
    protected InventoryService $inventoryService;
    protected StockMovementService $movementService;

    public function __construct(
        BorrowingService $borrowingService,
        InventoryService $inventoryService,
        StockMovementService $movementService
    ) {
        $this->middleware('auth');
        $this->borrowingService = $borrowingService;
        $this->inventoryService = $inventoryService;
        $this->movementService = $movementService;
    }

    /**
     * Display listing of loans
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status', 'Sedang Dipinjam');
        $exportType = $request->input('export');

        $query = Peminjamans::with(['barang.unit', 'inventoryItem', 'ruangan', 'user', 'pengembalian'])
            ->when($status, function ($q) use ($status) {
                if ($status === 'Semua') {
                    return;
                }
                $q->where('status', $status);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('kode_barang', 'like', "%{$keyword}%")
                      ->orWhere('nama_peminjam', 'like', "%{$keyword}%")
                      ->orWhereHas('barang', function ($q2) use ($keyword) {
                          $q2->where('nama', 'like', "%{$keyword}%")
                             ->orWhere('merek', 'like', "%{$keyword}%")
                             ->orWhere('kode_barang', 'like', "%{$keyword}%");
                      })
                      ->orWhereHas('inventoryItem', function ($q2) use ($keyword) {
                          $q2->where('serial_number', 'like', "%{$keyword}%");
                      })
                      ->orWhereHas('ruangan', function ($q2) use ($keyword) {
                          $q2->where('nama_ruangan', 'like', "%{$keyword}%");
                      });
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                $query->whereDate('tanggal_pinjam', '>=', $startDate);
            })
            ->when(!$startDate && $endDate, function ($query) use ($endDate) {
                $query->whereDate('tanggal_pinjam', '<=', $endDate);
            });

        // Ekspor
        if ($exportType) {
            $peminjaman = $query->orderBy('id', 'desc')->get();

            $peminjaman->transform(function ($item) {
                $now = Carbon::now();
                $tanggalKembali = Carbon::parse($item->tanggal_kembali);
                $item->tenggat = ($item->status === 'Sedang Dipinjam' && $now->gt($tanggalKembali))
                    ? 'Terlambat'
                    : 'Dalam Masa Pinjam';
                return $item;
            });

            if ($exportType === 'excel') {
                return Excel::download(new PeminjamanExport($peminjaman), 'laporan-data-peminjaman.xlsx');
            } elseif ($exportType === 'pdf') {
                $pdf = Pdf::loadView('pdf.peminjaman', ['peminjaman' => $peminjaman]);
                return $pdf->download('laporan-data-peminjaman.pdf');
            }
        }

        $peminjaman = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $peminjaman->getCollection()->transform(function ($item) {
            $now = Carbon::now();
            $tanggalKembali = Carbon::parse($item->tanggal_kembali);
            $item->tenggat = ($item->status === 'Sedang Dipinjam' && $now->gt($tanggalKembali))
                ? 'Terlambat'
                : 'Dalam Masa Pinjam';

            $totalReturned = $item->pengembalian->sum('jumlah');
            $item->returned_qty = $totalReturned;
            $item->outstanding_qty = max(0, (float)$item->jumlah - $totalReturned);

            return $item;
        });

        return view('peminjaman.index', compact('peminjaman', 'keyword', 'startDate', 'endDate', 'status'));
    }

    /**
     * Show form for creating a new loan
     */
    public function create()
    {
        $barang = Barangs::with(['unit', 'inventoryItems' => function ($q) {
            $q->where('status', 'available')->where('current_quantity', '>', 0);
        }])->where('is_active', true)->where('stok', '>', 0)->orderBy('nama', 'asc')->get();

        $ruangan = Ruangans::orderBy('nama_ruangan', 'asc')->get();

        return view('peminjaman.create', compact('barang', 'ruangan'));
    }

    /**
     * Store new loan transaction
     */
    public function store(StorePeminjamanRequest $request)
    {
        try {
            $peminjaman = $this->borrowingService->borrow([
                'barang_id' => (int) $request->id_barang,
                'inventory_item_id' => $request->inventory_item_id ? (int) $request->inventory_item_id : null,
                'jumlah' => (float) $request->jumlah,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'nama_peminjam' => $request->nama_peminjam,
                'ruangan_id' => $request->ruangan_id ? (int) $request->ruangan_id : null,
            ], Auth::id());

            Alert::success('Berhasil!', "Transaksi peminjaman #{$peminjaman->kode_barang} untuk {$peminjaman->nama_peminjam} berhasil dicatat.");
            return redirect()->route('peminjaman.index');
        } catch (Exception $e) {
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Show detail of loan transaction
     */
    public function show($id)
    {
        $peminjaman = Peminjamans::with(['barang.unit', 'inventoryItem', 'ruangan', 'user', 'pengembalian.user'])->findOrFail($id);
        $totalReturned = $peminjaman->pengembalian->sum('jumlah');
        $outstanding = max(0, (float) $peminjaman->jumlah - $totalReturned);

        return view('peminjaman.show', compact('peminjaman', 'totalReturned', 'outstanding'));
    }

    /**
     * Edit loan transaction
     */
    public function edit($id)
    {
        $peminjaman = Peminjamans::with(['barang.unit', 'inventoryItem', 'ruangan'])->findOrFail($id);
        $ruangan = Ruangans::orderBy('nama_ruangan', 'asc')->get();

        return view('peminjaman.edit', compact('peminjaman', 'ruangan'));
    }

    /**
     * Update loan metadata (dates, borrower name)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'nama_peminjam' => 'required|string|max:255',
        ]);

        try {
            $peminjaman = Peminjamans::findOrFail($id);
            $peminjaman->tanggal_pinjam = $request->tanggal_pinjam;
            $peminjaman->tanggal_kembali = $request->tanggal_kembali;
            $peminjaman->nama_peminjam = $request->nama_peminjam;
            $peminjaman->save();

            Alert::success('Berhasil!', 'Data peminjaman berhasil diperbarui.');
            return redirect()->route('peminjaman.show', $peminjaman->id);
        } catch (Exception $e) {
            Alert::error('Gagal!', $e->getMessage());
            return back();
        }
    }

    /**
     * Delete / void loan transaction
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $peminjaman = Peminjamans::lockForUpdate()->findOrFail($id);

            // Revert borrowed status/quantity
            $barang = Barangs::lockForUpdate()->findOrFail($peminjaman->id_barang);
            $qty = (float) $peminjaman->jumlah;

            if ($peminjaman->inventory_item_id) {
                $item = InventoryItem::lockForUpdate()->find($peminjaman->inventory_item_id);
                if ($item) {
                    $itemQtyBefore = (float) $item->current_quantity;
                    $itemQtyAfter = $itemQtyBefore + $qty;
                    $item->current_quantity = $itemQtyAfter;
                    if ($item->status === 'borrowed') {
                        $item->status = 'available';
                    }
                    $item->save();

                    $this->movementService->record(
                        $barang->id,
                        $item->id,
                        'adjustment',
                        $qty,
                        $itemQtyBefore,
                        $itemQtyAfter,
                        'peminjaman_void',
                        $peminjaman->id,
                        $item->ruangan_id,
                        Auth::id(),
                        "Pembatalan Peminjaman #{$peminjaman->kode_barang}"
                    );
                }
            } else {
                $qtyBefore = (float) $barang->stok;
                $qtyAfter = $qtyBefore + $qty;
                $barang->stok = $qtyAfter;
                $barang->save();

                if ($peminjaman->ruangan_id) {
                    $room = BarangRuangans::lockForUpdate()->firstOrNew([
                        'barang_id' => $barang->id,
                        'ruangan_id' => $peminjaman->ruangan_id,
                    ]);
                    $room->stok = (float) ($room->stok ?? 0) + $qty;
                    $room->save();
                }

                $this->movementService->record(
                    $barang->id,
                    null,
                    'adjustment',
                    $qty,
                    $qtyBefore,
                    $qtyAfter,
                    'peminjaman_void',
                    $peminjaman->id,
                    $peminjaman->ruangan_id,
                    Auth::id(),
                    "Pembatalan Peminjaman #{$peminjaman->kode_barang}"
                );
            }

            $peminjaman->delete();
            $this->inventoryService->syncMasterStock($barang->id);

            DB::commit();

            Alert::success('Berhasil!', 'Data peminjaman berhasil dibatalkan dan status barang dipulihkan.');
            return redirect()->route('peminjaman.index');
        } catch (Exception $e) {
            DB::rollBack();
            Alert::error('Gagal!', $e->getMessage());
            return redirect()->route('peminjaman.index');
        }
    }
}
