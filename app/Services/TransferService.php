<?php

namespace App\Services;

use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\LocationHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class TransferService
{
    protected StockMovementService $movementService;

    public function __construct(StockMovementService $movementService)
    {
        $this->movementService = $movementService;
    }

    /**
     * Transfer a serialized inventory item between rooms
     */
    public function transferSerialized(int $inventoryItemId, int $toRuanganId, ?string $keterangan, int $userId): InventoryItem
    {
        return DB::transaction(function () use ($inventoryItemId, $toRuanganId, $keterangan, $userId) {
            $item = InventoryItem::lockForUpdate()->with('barang')->findOrFail($inventoryItemId);
            $fromRuanganId = $item->ruangan_id;

            if ($fromRuanganId == $toRuanganId) {
                throw new Exception('Ruangan tujuan tidak boleh sama dengan ruangan asal.');
            }

            $item->ruangan_id = $toRuanganId;
            $item->save();

            // Record location history
            LocationHistory::create([
                'inventory_item_id' => $item->id,
                'from_ruangan_id' => $fromRuanganId,
                'to_ruangan_id' => $toRuanganId,
                'user_id' => $userId,
                'tanggal' => Carbon::now(),
                'keterangan' => $keterangan ?? 'Perpindahan ruangan unit serial',
            ]);

            // Record stock movement (transfer)
            $this->movementService->record(
                $item->barang_id,
                $item->id,
                'transfer',
                (float) $item->current_quantity,
                (float) $item->current_quantity,
                (float) $item->current_quantity,
                'transfer',
                null,
                $toRuanganId,
                $userId,
                $keterangan ?? "Perpindahan dari Ruangan #{$fromRuanganId} ke Ruangan #{$toRuanganId}"
            );

            return $item;
        });
    }

    /**
     * Transfer non-serialized stock between rooms
     */
    public function transferNonSerialized(int $barangId, int $fromRuanganId, int $toRuanganId, float $quantity, ?string $keterangan, int $userId): array
    {
        return DB::transaction(function () use ($barangId, $fromRuanganId, $toRuanganId, $quantity, $keterangan, $userId) {
            if ($fromRuanganId == $toRuanganId) {
                throw new Exception('Ruangan tujuan tidak boleh sama dengan ruangan asal.');
            }

            if ($quantity <= 0) {
                throw new Exception('Jumlah transfer harus lebih besar dari 0.');
            }

            $barang = Barangs::lockForUpdate()->findOrFail($barangId);

            $sourceRoom = BarangRuangans::lockForUpdate()->where('barang_id', $barangId)
                ->where('ruangan_id', $fromRuanganId)
                ->first();

            if (!$sourceRoom || (float) $sourceRoom->stok < $quantity) {
                $avail = $sourceRoom ? $sourceRoom->stok : 0;
                throw new Exception("Stok di ruangan asal tidak mencukupi. Tersedia: {$avail}, diminta: {$quantity}.");
            }

            // Deduct from source room
            $sourceRoom->stok = (float) $sourceRoom->stok - $quantity;
            $sourceRoom->save();

            // Add to destination room
            $destRoom = BarangRuangans::lockForUpdate()->firstOrNew([
                'barang_id' => $barangId,
                'ruangan_id' => $toRuanganId,
            ]);
            $destRoom->stok = (float) ($destRoom->stok ?? 0) + $quantity;
            $destRoom->save();

            // Record stock movement
            $this->movementService->record(
                $barangId,
                null,
                'transfer',
                $quantity,
                (float) $barang->stok,
                (float) $barang->stok,
                'transfer',
                null,
                $toRuanganId,
                $userId,
                $keterangan ?? "Transfer {$quantity} {$barang->satuan?->symbol} dari Ruang #{$fromRuanganId} ke Ruang #{$toRuanganId}"
            );

            return [
                'source' => $sourceRoom,
                'destination' => $destRoom,
            ];
        });
    }
}
