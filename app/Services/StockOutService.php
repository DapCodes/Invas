<?php

namespace App\Services;

use App\Models\Barangs;
use App\Models\BarangKeluars;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class StockOutService
{
    protected StockMovementService $movementService;
    protected InventoryService $inventoryService;

    public function __construct(StockMovementService $movementService, InventoryService $inventoryService)
    {
        $this->movementService = $movementService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Process outgoing stock for non-serialized items
     */
    public function processNonSerialized(array $data, int $userId): BarangKeluars
    {
        return DB::transaction(function () use ($data, $userId) {
            $barang = Barangs::lockForUpdate()->findOrFail($data['barang_id']);

            $quantity = (float) $data['jumlah'];
            if ($quantity <= 0) {
                throw new Exception('Jumlah barang keluar harus lebih besar dari 0.');
            }

            if ((float) $barang->stok < $quantity) {
                throw new Exception("Stok master tidak mencukupi. Tersedia: {$barang->stok}, diminta: {$quantity}.");
            }

            $ruanganId = $data['ruangan_id'] ?? null;
            $tanggal = $data['tanggal_keluar'] ?? Carbon::now()->toDateString();
            $keterangan = $data['keterangan'] ?? 'Barang Keluar';

            // Check room stock
            if ($ruanganId) {
                $barangRuangan = BarangRuangans::lockForUpdate()
                    ->where('barang_id', $barang->id)
                    ->where('ruangan_id', $ruanganId)
                    ->first();

                if (!$barangRuangan || (float) $barangRuangan->stok < $quantity) {
                    $avail = $barangRuangan ? $barangRuangan->stok : 0;
                    throw new Exception("Stok di ruangan yang dipilih tidak mencukupi. Tersedia: {$avail}, diminta: {$quantity}.");
                }

                $barangRuangan->stok = (float) $barangRuangan->stok - $quantity;
                $barangRuangan->save();
            }

            $qtyBefore = (float) $barang->stok;
            $qtyAfter = $qtyBefore - $quantity;

            $barang->stok = $qtyAfter;
            $barang->save();

            // Generate transaction code
            $lastRecord = BarangKeluars::latest('id')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $kodeTrans = 'BK-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

            $barangKeluar = BarangKeluars::create([
                'kode_barang' => $kodeTrans,
                'id_barang' => $barang->id,
                'inventory_item_id' => null,
                'jumlah' => $quantity,
                'satuan_id' => $barang->satuan_id,
                'tanggal_keluar' => $tanggal,
                'keterangan' => $keterangan,
                'ruangan_id' => $ruanganId,
                'id_user' => $userId,
            ]);

            // Record movement ledger
            $this->movementService->record(
                $barang->id,
                null,
                'out',
                -$quantity,
                $qtyBefore,
                $qtyAfter,
                'barang_keluar',
                $barangKeluar->id,
                $ruanganId,
                $userId,
                $keterangan,
                $tanggal
            );

            return $barangKeluar;
        });
    }

    /**
     * Process outgoing stock for serialized items (single unit or partial cable consumption)
     */
    public function processSerialized(int $inventoryItemId, float $quantity, array $metaData, int $userId): BarangKeluars
    {
        return DB::transaction(function () use ($inventoryItemId, $quantity, $metaData, $userId) {
            $item = InventoryItem::lockForUpdate()->with('barang')->findOrFail($inventoryItemId);
            $barang = Barangs::lockForUpdate()->findOrFail($item->barang_id);

            if ($quantity <= 0) {
                throw new Exception('Jumlah barang keluar harus lebih besar dari 0.');
            }

            if ($item->status !== 'available' && $item->status !== 'in_use') {
                throw new Exception("Unit serial {$item->serial_number} sedang dalam status '{$item->status}' dan tidak dapat dikeluarkan.");
            }

            $currentQty = (float) $item->current_quantity;
            if ($quantity > $currentQty) {
                throw new Exception("Jumlah keluar ({$quantity}) melebihi saldo quantity yang tersedia pada serial {$item->serial_number} ({$currentQty}).");
            }

            $tanggal = $metaData['tanggal_keluar'] ?? Carbon::now()->toDateString();
            $keterangan = $metaData['keterangan'] ?? 'Pengeluaran unit serial';
            $ruanganId = $metaData['ruangan_id'] ?? $item->ruangan_id;

            $qtyBefore = $currentQty;
            $qtyAfter = $currentQty - $quantity;

            $item->current_quantity = $qtyAfter;
            if ($qtyAfter <= 0) {
                $item->status = 'out';
                $item->tanggal_keluar = $tanggal;
            }
            $item->save();

            // Generate BK code
            $lastRecord = BarangKeluars::latest('id')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $kodeTrans = 'BK-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

            $barangKeluar = BarangKeluars::create([
                'kode_barang' => $kodeTrans,
                'id_barang' => $barang->id,
                'inventory_item_id' => $item->id,
                'jumlah' => $quantity,
                'satuan_id' => $item->satuan_id ?? $barang->satuan_id,
                'tanggal_keluar' => $tanggal,
                'keterangan' => $keterangan . " (SN: {$item->serial_number})",
                'ruangan_id' => $ruanganId,
                'id_user' => $userId,
            ]);

            // Record movement ledger
            $this->movementService->record(
                $barang->id,
                $item->id,
                'out',
                -$quantity,
                $qtyBefore,
                $qtyAfter,
                'barang_keluar',
                $barangKeluar->id,
                $ruanganId,
                $userId,
                $keterangan . " (SN: {$item->serial_number})",
                $tanggal
            );

            // Sync master stock
            $this->inventoryService->syncMasterStock($barang->id);

            return $barangKeluar;
        });
    }
}
