<?php

namespace App\Services;

use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\Peminjamans;
use App\Models\PeminjamanDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class BorrowingService
{
    protected StockMovementService $movementService;
    protected InventoryService $inventoryService;

    public function __construct(StockMovementService $movementService, InventoryService $inventoryService)
    {
        $this->movementService = $movementService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Process borrowing (supports both serialized and non-serialized)
     */
    public function borrow(array $data, int $userId): Peminjamans
    {
        return DB::transaction(function () use ($data, $userId) {
            $barang = Barangs::lockForUpdate()->findOrFail($data['barang_id']);
            $quantity = (float) $data['jumlah'];
            $inventoryItemId = $data['inventory_item_id'] ?? null;
            $ruanganId = $data['ruangan_id'] ?? null;
            $tanggalPinjam = $data['tanggal_pinjam'] ?? Carbon::now()->toDateString();
            $tanggalKembali = $data['tanggal_kembali'] ?? null;
            $namaPeminjam = $data['nama_peminjam'];

            if ($quantity <= 0) {
                throw new Exception('Jumlah peminjaman harus lebih besar dari 0.');
            }

            $item = null;
            if ($barang->has_serial_number || $inventoryItemId) {
                $item = InventoryItem::lockForUpdate()->findOrFail($inventoryItemId);
                if ($item->status !== 'available') {
                    throw new Exception("Unit serial {$item->serial_number} sedang dalam status '{$item->status}' dan tidak dapat dipinjam.");
                }

                if (!$ruanganId && $item->ruangan_id) {
                    $ruanganId = $item->ruangan_id;
                }
            }

            // Generate BP transaction code
            $lastRecord = Peminjamans::latest('id')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $kodeTrans = 'BP-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

            $peminjaman = Peminjamans::create([
                'kode_barang' => $kodeTrans,
                'id_barang' => $barang->id,
                'inventory_item_id' => $inventoryItemId,
                'jumlah' => $quantity,
                'satuan_id' => $barang->satuan_id,
                'tanggal_pinjam' => $tanggalPinjam,
                'tanggal_kembali' => $tanggalKembali,
                'nama_peminjam' => $namaPeminjam,
                'status' => 'Sedang Dipinjam',
                'ruangan_id' => $ruanganId,
                'id_user' => $userId,
            ]);

            if ($item) {
                $itemQty = (float) $item->current_quantity;
                if ($quantity > $itemQty) {
                    throw new Exception("Jumlah peminjaman ({$quantity}) melebihi stok unit serial ({$itemQty}).");
                }

                $qtyBefore = $itemQty;
                $qtyAfter = $itemQty - $quantity;

                // If fully borrowed or single unit
                if ($qtyAfter <= 0) {
                    $item->status = 'borrowed';
                }
                $item->save();

                PeminjamanDetail::create([
                    'peminjaman_id' => $peminjaman->id,
                    'barang_id' => $barang->id,
                    'inventory_item_id' => $item->id,
                    'quantity' => $quantity,
                    'returned_quantity' => 0,
                    'status' => 'borrowed',
                ]);

                // Movement
                $this->movementService->record(
                    $barang->id,
                    $item->id,
                    'borrow',
                    -$quantity,
                    $qtyBefore,
                    $qtyAfter,
                    'peminjaman',
                    $peminjaman->id,
                    $item->ruangan_id,
                    $userId,
                    "Dipinjam oleh {$namaPeminjam} (SN: {$item->serial_number})",
                    $tanggalPinjam
                );
            } else {
                // Non-serialized borrowing
                if ((float) $barang->stok < $quantity) {
                    throw new Exception("Stok barang tidak mencukupi untuk dipinjam.");
                }

                if ($ruanganId) {
                    $barangRuangan = BarangRuangans::lockForUpdate()
                        ->where('barang_id', $barang->id)
                        ->where('ruangan_id', $ruanganId)
                        ->first();

                    if (!$barangRuangan || (float) $barangRuangan->stok < $quantity) {
                        throw new Exception("Stok di ruangan tidak mencukupi untuk dipinjam.");
                    }

                    $barangRuangan->stok = (float) $barangRuangan->stok - $quantity;
                    $barangRuangan->save();
                }

                $qtyBefore = (float) $barang->stok;
                $qtyAfter = $qtyBefore - $quantity;

                $barang->stok = $qtyAfter;
                $barang->save();

                PeminjamanDetail::create([
                    'peminjaman_id' => $peminjaman->id,
                    'barang_id' => $barang->id,
                    'inventory_item_id' => null,
                    'quantity' => $quantity,
                    'returned_quantity' => 0,
                    'status' => 'borrowed',
                ]);

                $this->movementService->record(
                    $barang->id,
                    null,
                    'borrow',
                    -$quantity,
                    $qtyBefore,
                    $qtyAfter,
                    'peminjaman',
                    $peminjaman->id,
                    $ruanganId,
                    $userId,
                    "Dipinjam oleh {$namaPeminjam}",
                    $tanggalPinjam
                );
            }

            // Sync master stock
            $this->inventoryService->syncMasterStock($barang->id);

            return $peminjaman;
        });
    }
}
