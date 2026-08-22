<?php

namespace App\Services;

use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\Peminjamans;
use App\Models\PeminjamanDetail;
use App\Models\Pengembalians;
use App\Models\PengembalianDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    protected StockMovementService $movementService;
    protected InventoryService $inventoryService;

    public function __construct(StockMovementService $movementService, InventoryService $inventoryService)
    {
        $this->movementService = $movementService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Process return of borrowed items (supports full, partial, condition recording)
     */
    public function processReturn(int $peminjamanId, array $data, int $userId): Pengembalians
    {
        return DB::transaction(function () use ($peminjamanId, $data, $userId) {
            $peminjaman = Peminjamans::lockForUpdate()->with('details')->findOrFail($peminjamanId);
            $barang = Barangs::lockForUpdate()->findOrFail($peminjaman->id_barang);

            $jumlahKembali = (float) $data['jumlah_kembali'];
            $tanggalKembali = $data['tanggal_kembali'] ?? Carbon::now()->toDateString();
            $kondisi = $data['kondisi'] ?? 'Baik';
            $keterangan = $data['keterangan'] ?? 'Pengembalian barang';
            $ruanganId = $data['ruangan_id'] ?? $peminjaman->ruangan_id;

            if ($jumlahKembali <= 0) {
                throw new Exception('Jumlah pengembalian harus lebih besar dari 0.');
            }

            // Total borrowed vs previously returned
            $totalBorrowed = (float) $peminjaman->jumlah;
            $previouslyReturned = Pengembalians::where('id_peminjam', $peminjaman->id)->sum('jumlah');
            $remainingToReturn = $totalBorrowed - $previouslyReturned;

            if ($jumlahKembali > $remainingToReturn) {
                throw new Exception("Jumlah kembali ({$jumlahKembali}) melebihi sisa pinjaman ({$remainingToReturn}).");
            }

            $selisih = max(0, $remainingToReturn - $jumlahKembali);

            // Generate BB code
            $lastRecord = Pengembalians::latest('id')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $kodeTrans = 'BB-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

            $pengembalian = Pengembalians::create([
                'kode_barang' => $kodeTrans,
                'id_peminjam' => $peminjaman->id,
                'id_barang' => $barang->id,
                'inventory_item_id' => $peminjaman->inventory_item_id,
                'jumlah' => $jumlahKembali,
                'selisih' => $selisih,
                'satuan_id' => $barang->satuan_id,
                'tanggal_kembali' => $tanggalKembali,
                'nama_peminjam' => $peminjaman->nama_peminjam,
                'status' => 'Sudah Dikembalikan',
                'kondisi' => $kondisi,
                'ruangan_id' => $ruanganId,
                'id_user' => $userId,
            ]);

            // Serialized return
            if ($peminjaman->inventory_item_id) {
                $item = InventoryItem::lockForUpdate()->findOrFail($peminjaman->inventory_item_id);
                $itemQtyBefore = (float) $item->current_quantity;
                $itemQtyAfter = $itemQtyBefore + $jumlahKembali;

                $item->current_quantity = $itemQtyAfter;

                if ($kondisi === 'Rusak') {
                    $item->status = 'damaged';
                } elseif ($kondisi === 'Hilang') {
                    $item->status = 'lost';
                } else {
                    $item->status = 'available';
                }

                if ($ruanganId) {
                    $item->ruangan_id = $ruanganId;
                }
                $item->save();

                // Details
                $peminjamanDetail = PeminjamanDetail::where('peminjaman_id', $peminjaman->id)
                    ->where('inventory_item_id', $item->id)
                    ->first();

                if ($peminjamanDetail) {
                    $peminjamanDetail->returned_quantity = (float) $peminjamanDetail->returned_quantity + $jumlahKembali;
                    $peminjamanDetail->status = ($peminjamanDetail->returned_quantity >= $peminjamanDetail->quantity)
                        ? 'returned' : 'partially_returned';
                    $peminjamanDetail->save();
                }

                PengembalianDetail::create([
                    'pengembalian_id' => $pengembalian->id,
                    'peminjaman_detail_id' => $peminjamanDetail ? $peminjamanDetail->id : null,
                    'barang_id' => $barang->id,
                    'inventory_item_id' => $item->id,
                    'quantity' => $jumlahKembali,
                    'selisih' => $selisih,
                    'kondisi' => $kondisi,
                    'keterangan' => $keterangan,
                ]);

                // Movement
                $this->movementService->record(
                    $barang->id,
                    $item->id,
                    'return',
                    $jumlahKembali,
                    $itemQtyBefore,
                    $itemQtyAfter,
                    'pengembalian',
                    $pengembalian->id,
                    $ruanganId,
                    $userId,
                    "Dikembalikan oleh {$peminjaman->nama_peminjam} (Kondisi: {$kondisi}, SN: {$item->serial_number})",
                    $tanggalKembali
                );
            } else {
                // Non-serialized return
                $qtyBefore = (float) $barang->stok;
                $qtyAfter = $qtyBefore + $jumlahKembali;

                $barang->stok = $qtyAfter;
                $barang->save();

                if ($ruanganId) {
                    $barangRuangan = BarangRuangans::lockForUpdate()->firstOrNew([
                        'barang_id' => $barang->id,
                        'ruangan_id' => $ruanganId,
                    ]);
                    $barangRuangan->stok = (float) ($barangRuangan->stok ?? 0) + $jumlahKembali;
                    $barangRuangan->save();
                }

                $peminjamanDetail = PeminjamanDetail::where('peminjaman_id', $peminjaman->id)->first();
                if ($peminjamanDetail) {
                    $peminjamanDetail->returned_quantity = (float) $peminjamanDetail->returned_quantity + $jumlahKembali;
                    $peminjamanDetail->status = ($peminjamanDetail->returned_quantity >= $peminjamanDetail->quantity)
                        ? 'returned' : 'partially_returned';
                    $peminjamanDetail->save();
                }

                PengembalianDetail::create([
                    'pengembalian_id' => $pengembalian->id,
                    'peminjaman_detail_id' => $peminjamanDetail ? $peminjamanDetail->id : null,
                    'barang_id' => $barang->id,
                    'inventory_item_id' => null,
                    'quantity' => $jumlahKembali,
                    'selisih' => $selisih,
                    'kondisi' => $kondisi,
                    'keterangan' => $keterangan,
                ]);

                $this->movementService->record(
                    $barang->id,
                    null,
                    'return',
                    $jumlahKembali,
                    $qtyBefore,
                    $qtyAfter,
                    'pengembalian',
                    $pengembalian->id,
                    $ruanganId,
                    $userId,
                    "Dikembalikan oleh {$peminjaman->nama_peminjam} (Kondisi: {$kondisi})",
                    $tanggalKembali
                );
            }

            // Update parent peminjaman status
            $totalReturnedNow = $previouslyReturned + $jumlahKembali;
            if ($totalReturnedNow >= $totalBorrowed) {
                $peminjaman->status = 'Sudah Dikembalikan';
            }
            $peminjaman->save();

            // Sync master stock
            $this->inventoryService->syncMasterStock($barang->id);

            return $pengembalian;
        });
    }
}
