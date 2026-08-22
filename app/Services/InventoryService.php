<?php

namespace App\Services;

use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Recalculate and cache master stock on `barangs.stok`
     */
    public function syncMasterStock(int $barangId): float
    {
        $barang = Barangs::lockForUpdate()->find($barangId);
        if (!$barang) {
            return 0.0;
        }

        if ($barang->has_serial_number) {
            // Aggregate from active inventory items (all except permanently removed ones)
            // 'borrowed' is still counted as part of master total (it belongs to the org)
            // 'out', 'damaged', 'lost', 'depleted' are excluded (permanently gone)
            $totalStock = InventoryItem::where('barang_id', $barangId)
                ->whereNotIn('status', ['out', 'damaged', 'lost', 'depleted'])
                ->sum('current_quantity');
        } else {
            // Aggregate from barang_ruangans or keep master stock
            $roomTotal = BarangRuangans::where('barang_id', $barangId)->sum('stok');
            $totalStock = $roomTotal > 0 ? $roomTotal : (float) $barang->stok;
        }

        $barang->stok = $totalStock;
        $barang->save();

        return (float) $totalStock;
    }

    /**
     * Get stock summary for a master item
     */
    public function getStockSummary(Barangs $barang): array
    {
        if ($barang->has_serial_number) {
            $items = InventoryItem::where('barang_id', $barang->id)->get();
            $totalItems = $items->count();
            $totalStock = $items->sum('current_quantity');
            $availableStock = $items->where('status', 'available')->sum('current_quantity');
            $borrowedStock = $items->where('status', 'borrowed')->sum('current_quantity');
            $inUseStock = $items->where('status', 'in_use')->sum('current_quantity');
            $outStock = $items->where('status', 'out')->sum('current_quantity');
            $damagedStock = $items->where('status', 'damaged')->sum('current_quantity');
            $lostStock = $items->where('status', 'lost')->sum('current_quantity');
            $uniqueLocations = $items->pluck('ruangan_id')->filter()->unique()->count();
        } else {
            $totalItems = 0;
            $totalStock = (float) $barang->stok;
            $rooms = BarangRuangans::where('barang_id', $barang->id)->get();
            $uniqueLocations = $rooms->where('stok', '>', 0)->count();
            $availableStock = $totalStock;
            $borrowedStock = 0;
            $inUseStock = 0;
            $outStock = 0;
            $damagedStock = 0;
            $lostStock = 0;
        }

        return [
            'total_stock' => (float) $totalStock,
            'available_stock' => (float) $availableStock,
            'borrowed_stock' => (float) $borrowedStock,
            'in_use_stock' => (float) $inUseStock,
            'out_stock' => (float) $outStock,
            'damaged_stock' => (float) $damagedStock,
            'lost_stock' => (float) $lostStock,
            'total_serials' => $totalItems,
            'total_locations' => $uniqueLocations,
        ];
    }
}
