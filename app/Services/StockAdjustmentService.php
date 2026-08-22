<?php

namespace App\Services;

use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService
{
    protected StockMovementService $movementService;
    protected InventoryService $inventoryService;

    public function __construct(StockMovementService $movementService, InventoryService $inventoryService)
    {
        $this->movementService = $movementService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Adjust serialized inventory item quantity / status
     */
    public function adjustSerialized(int $inventoryItemId, float $newQuantity, ?string $newStatus, string $alasan, int $userId): InventoryItem
    {
        return DB::transaction(function () use ($inventoryItemId, $newQuantity, $newStatus, $alasan, $userId) {
            $item = InventoryItem::lockForUpdate()->with('barang')->findOrFail($inventoryItemId);

            if ($newQuantity < 0) {
                throw new Exception('Quantity hasil penyesuaian tidak boleh negatif.');
            }

            $qtyBefore = (float) $item->current_quantity;
            $delta = $newQuantity - $qtyBefore;

            $item->current_quantity = $newQuantity;
            if ($newStatus) {
                $item->status = $newStatus;
            } elseif ($newQuantity == 0 && $item->status === 'available') {
                $item->status = 'depleted';
            }
            $item->save();

            // Record adjustment movement
            $this->movementService->record(
                $item->barang_id,
                $item->id,
                'adjustment',
                $delta,
                $qtyBefore,
                $newQuantity,
                'adjustment',
                null,
                $item->ruangan_id,
                $userId,
                "Penyesuaian stok (SN: {$item->serial_number}): {$alasan}"
            );

            // Sync master stock
            $this->inventoryService->syncMasterStock($item->barang_id);

            return $item;
        });
    }

    /**
     * Adjust non-serialized stock
     */
    public function adjustNonSerialized(int $barangId, ?int $ruanganId, float $newQuantity, string $alasan, int $userId): Barangs
    {
        return DB::transaction(function () use ($barangId, $ruanganId, $newQuantity, $alasan, $userId) {
            $barang = Barangs::lockForUpdate()->findOrFail($barangId);

            if ($newQuantity < 0) {
                throw new Exception('Quantity hasil penyesuaian tidak boleh negatif.');
            }

            $qtyBefore = (float) $barang->stok;
            $delta = $newQuantity - $qtyBefore;

            $barang->stok = $newQuantity;
            $barang->save();

            if ($ruanganId) {
                $room = BarangRuangans::lockForUpdate()->firstOrNew([
                    'barang_id' => $barangId,
                    'ruangan_id' => $ruanganId,
                ]);
                $room->stok = $newQuantity;
                $room->save();
            }

            $this->movementService->record(
                $barangId,
                null,
                'adjustment',
                $delta,
                $qtyBefore,
                $newQuantity,
                'adjustment',
                null,
                $ruanganId,
                $userId,
                "Penyesuaian stok: {$alasan}"
            );

            return $barang;
        });
    }
}
