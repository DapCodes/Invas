<?php

namespace App\Http\Controllers;

use App\Exports\PengembalianExport;
use App\Http\Requests\StorePengembalianRequest;
use App\Models\Barangs;
use App\Models\Peminjamans;
use App\Models\Pengembalians;
use App\Models\Ruangans;
use App\Services\InventoryService;
use App\Services\ReturnService;
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

class PengembalianController extends Controller
{
    protected ReturnService $returnService;
    protected InventoryService $inventoryService;
    protected StockMovementService $movementService;

    public function __construct(
        ReturnService $returnService,
        InventoryService $inventoryService,
        StockMovementService $movementService
    ) {
        $this->middleware('auth');
        $this->returnService = $returnService;
        $this->inventoryService = $inventoryService;
        $this->movementService = $movementService;
    }

    /**
     * Display listing of return records
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $kondisi = $request->input('kondisi');
        $exportType = $request->input('export');

        $query = Pengembalians::with(['barang.unit', 'inventoryItem', 'ruangan', 'user', 'peminjamans'])
            ->when($kondisi, function ($q) use ($kondisi) {
                $q->where('kondisi', $kondisi);
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
                $query->whereBetween('tanggal_kembali', [$startDate, $endDate]);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                $query->whereDate('tanggal_kembali', '>=', $startDate);
            })
            ->when(!$startDate && $endDate, function ($query) use ($endDate) {
                $query->whereDate('tanggal_kembali', '<=', $endDate);
            });

        // EXPORT
        if ($exportType) {
            $dataExport = $query->orderBy('id', 'desc')->get();

            if ($exportType === 'excel') {
                return Excel::download(new PengembalianExport($dataExport), 'laporan-data-pengembalian.xlsx');
            } elseif ($exportType === 'pdf') {
                $pdf = Pdf::loadView('pdf.pengembalian', ['pengembalian' => $dataExport]);
                return $pdf->download('laporan-data-pengembalian.pdf');
            }
        }

        $pengembalian = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('pengembalian.index', compact('pengembalian', 'keyword', 'startDate', 'endDate', 'kondisi'));
    }

    /**
     * Show form for returning items from an active loan
     */
    public function create(Request $request)
    {
        $peminjamanId = $request->input('peminjaman_id');
        $selectedPeminjaman = $peminjamanId ? Peminjamans::with(['barang.unit', 'inventoryItem', 'ruangan', 'pengembalian'])->find($peminjamanId) : null;

        // Active loans (with status Sedang Dipinjam)
        $activeLoans = Peminjamans::with(['barang.unit', 'inventoryItem', 'ruangan', 'pengembalian'])
            ->where('status', 'Sedang Dipinjam')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($loan) {
                $totalReturned = $loan->pengembalian->sum('jumlah');
                $loan->outstanding = max(0, (float) $loan->jumlah - $totalReturned);
                return $loan;
            })
            ->filter(function ($loan) {
                return $loan->outstanding > 0;
            });

        $ruangan = Ruangans::orderBy('nama_ruangan', 'asc')->get();

        return view('pengembalian.create', compact('selectedPeminjaman', 'activeLoans', 'ruangan'));
    }

    /**
     * Store return transaction
     */
    public function store(StorePengembalianRequest $request)
    {
        try {
            $pengembalian = $this->returnService->processReturn((int) $request->id_peminjam, [
                'jumlah_kembali' => (float) $request->jumlah_kembali,
                'tanggal_kembali' => $request->tanggal_kembali,
                'kondisi' => $request->kondisi,
                'keterangan' => $request->keterangan,
                'ruangan_id' => $request->ruangan_id ? (int) $request->ruangan_id : null,
            ], Auth::id());

            Alert::success('Berhasil!', "Pengembalian #{$pengembalian->kode_barang} berhasil dicatat (Kondisi: {$pengembalian->kondisi}).");
            return redirect()->route('pengembalian.index');
        } catch (Exception $e) {
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Detail of return transaction
     */
    public function show($id)
    {
        $pengembalian = Pengembalians::with(['barang.unit', 'inventoryItem', 'ruangan', 'user', 'peminjamans'])->findOrFail($id);
        return view('pengembalian.show', compact('pengembalian'));
    }

    /**
     * Void return transaction
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pengembalian = Pengembalians::lockForUpdate()->findOrFail($id);
            $peminjaman = Peminjamans::lockForUpdate()->find($pengembalian->id_peminjam);

            if ($peminjaman) {
                $peminjaman->status = 'Sedang Dipinjam';
                $peminjaman->save();
            }

            $barang = Barangs::lockForUpdate()->findOrFail($pengembalian->id_barang);
            $qty = (float) $pengembalian->jumlah;

            if ($pengembalian->inventory_item_id) {
                $item = \App\Models\InventoryItem::lockForUpdate()->find($pengembalian->inventory_item_id);
                if ($item) {
                    $itemQtyBefore = (float) $item->current_quantity;
                    $itemQtyAfter = max(0, $itemQtyBefore - $qty);
                    $item->current_quantity = $itemQtyAfter;
                    $item->status = 'borrowed';
                    $item->save();

                    $this->movementService->record(
                        $barang->id,
                        $item->id,
                        'adjustment',
                        -$qty,
                        $itemQtyBefore,
                        $itemQtyAfter,
                        'pengembalian_void',
                        $pengembalian->id,
                        $pengembalian->ruangan_id,
                        Auth::id(),
                        "Pembatalan Pengembalian #{$pengembalian->kode_barang}"
                    );
                }
            } else {
                $qtyBefore = (float) $barang->stok;
                $qtyAfter = max(0, $qtyBefore - $qty);
                $barang->stok = $qtyAfter;
                $barang->save();

                $this->movementService->record(
                    $barang->id,
                    null,
                    'adjustment',
                    -$qty,
                    $qtyBefore,
                    $qtyAfter,
                    'pengembalian_void',
                    $pengembalian->id,
                    $pengembalian->ruangan_id,
                    Auth::id(),
                    "Pembatalan Pengembalian #{$pengembalian->kode_barang}"
                );
            }

            $pengembalian->delete();
            $this->inventoryService->syncMasterStock($barang->id);

            DB::commit();

            Alert::success('Berhasil!', 'Data pengembalian berhasil dibatalkan.');
            return redirect()->route('pengembalian.index');
        } catch (Exception $e) {
            DB::rollBack();
            Alert::error('Gagal!', $e->getMessage());
            return redirect()->route('pengembalian.index');
        }
    }
}
