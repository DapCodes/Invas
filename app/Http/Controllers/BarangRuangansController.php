<?php

namespace App\Http\Controllers;

use App\Exports\BarangRuanganExport;
use App\Http\Requests\StoreTransferRequest;
use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\Ruangans;
use App\Services\TransferService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

Carbon::setLocale('id');

class BarangRuangansController extends Controller
{
    protected TransferService $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->middleware('auth');
        $this->transferService = $transferService;
    }

    /**
     * Display listing of stocks in rooms
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $exportType = $request->input('export');
        $byClass = $request->input('byClass');

        $barangRuanganQuery = BarangRuangans::with(['ruangan', 'barang.unit'])
            ->where('stok', '>', 0)
            ->when($byClass, function ($q) use ($byClass) {
                $q->where('ruangan_id', $byClass);
            })
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->whereHas('ruangan', function ($r) use ($keyword) {
                        $r->where('nama_ruangan', 'like', "%{$keyword}%")
                          ->orWhere('deskripsi', 'like', "%{$keyword}%");
                    })->orWhereHas('barang', function ($b) use ($keyword) {
                        $b->where('nama', 'like', "%{$keyword}%")
                          ->orWhere('merek', 'like', "%{$keyword}%")
                          ->orWhere('kode_barang', 'like', "%{$keyword}%");
                    });
                });
            });

        // Export jika diminta
        if ($exportType) {
            $barangRuangan = $barangRuanganQuery->get();

            if ($exportType == 'excel') {
                return Excel::download(new BarangRuanganExport($barangRuangan), 'laporan-barang-ruangan.xlsx');
            }

            if ($exportType == 'pdf') {
                $pdf = Pdf::loadView('pdf.barangruangan', ['barangRuangan' => $barangRuangan]);
                return $pdf->download('laporan-barang-ruangan.pdf');
            }
        }

        $barangRuangan = $barangRuanganQuery->paginate(15)->withQueryString();

        // Also query serialized items per room if filtering by room
        $serializedInRoom = [];
        if ($byClass) {
            $serializedInRoom = InventoryItem::with(['barang.unit'])
                ->where('ruangan_id', $byClass)
                ->whereNotIn('status', ['lost', 'damaged', 'depleted'])
                ->orderBy('serial_number', 'asc')
                ->get();
        }

        $ruangan = Ruangans::orderBy('nama_ruangan', 'asc')->get();
        $barangsNonSerial = Barangs::where('has_serial_number', false)->where('stok', '>', 0)->get();

        return view('barangruangan.index', compact('barangRuangan', 'serializedInRoom', 'ruangan', 'keyword', 'byClass', 'barangsNonSerial'));
    }

    /**
     * Transfer stock between rooms (handles both non-serialized and serialized)
     */
    public function transfer(StoreTransferRequest $request)
    {
        try {
            if ($request->type === 'serialized') {
                $item = $this->transferService->transferSerialized(
                    (int) $request->inventory_item_id,
                    (int) $request->to_ruangan_id,
                    $request->keterangan,
                    Auth::id()
                );
                Alert::success('Berhasil!', "Unit serial {$item->serial_number} berhasil dipindahkan ke ruangan baru.");
            } else {
                $this->transferService->transferNonSerialized(
                    (int) $request->barang_id,
                    (int) $request->from_ruangan_id,
                    (int) $request->to_ruangan_id,
                    (float) $request->quantity,
                    $request->keterangan,
                    Auth::id()
                );
                Alert::success('Berhasil!', "Stok barang berhasil dipindahkan ke ruangan tujuan.");
            }

            return redirect()->back();
        } catch (Exception $e) {
            Alert::error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $barangRuangan = BarangRuangans::with(['barang.unit', 'ruangan'])->findOrFail($id);
        return view('barangruangan.show', compact('barangRuangan'));
    }
}
