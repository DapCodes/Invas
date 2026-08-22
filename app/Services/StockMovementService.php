<?php

namespace App\Services;

use App\Models\StockMovement;
use Carbon\Carbon;

class StockMovementService
{
    /**
     * Record a stock movement ledger entry.
     */
    public function record(
        int $barangId,
        ?int $inventoryItemId,
        string $type,
        float $quantity,
        float $quantityBefore,
        float $quantityAfter,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $ruanganId = null,
        ?int $userId = null,
        ?string $keterangan = null,
        $tanggal = null
    ): StockMovement {
        return StockMovement::create([
            'barang_id' => $barangId,
            'inventory_item_id' => $inventoryItemId,
            'type' => strtolower($type),
            'quantity' => $quantity,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'ruangan_id' => $ruanganId,
            'user_id' => $userId,
            'tanggal' => $tanggal ? Carbon::parse($tanggal) : Carbon::now(),
            'keterangan' => $keterangan,
        ]);
    }
}
