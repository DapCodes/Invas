<?php

namespace App\Services;

use App\Models\Barangs;
use App\Models\BarangMasuks;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class StockInService
{
    protected StockMovementService $movementService;
    protected InventoryService $inventoryService;

    public function __construct(StockMovementService $movementService, InventoryService $inventoryService)
    {
        $this->movementService = $movementService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Process incoming stock for non-serialized items
     */
    public function processNonSerialized(array $data, int $userId): BarangMasuks
    {
        return DB::transaction(function () use ($data, $userId) {
            $barang = Barangs::lockForUpdate()->findOrFail($data['barang_id']);

            $quantity = (float) $data['jumlah'];
            if ($quantity <= 0) {
                throw new Exception('Jumlah barang masuk harus lebih besar dari 0.');
            }

            $ruanganId = $data['ruangan_id'] ?? null;
            $tanggal = $data['tanggal_masuk'] ?? Carbon::now()->toDateString();
            $keterangan = $data['keterangan'] ?? 'Barang Masuk';

            $qtyBefore = (float) $barang->stok;
            $qtyAfter = $qtyBefore + $quantity;

            // Update master stock
            $barang->stok = $qtyAfter;
            if (isset($data['satuan_id'])) {
                $barang->satuan_id = $data['satuan_id'];
            }
            $barang->save();

            // Update room stock if specified
            if ($ruanganId) {
                $barangRuangan = BarangRuangans::lockForUpdate()->firstOrNew([
                    'barang_id' => $barang->id,
                    'ruangan_id' => $ruanganId,
                ]);
                $barangRuangan->stok = (float) ($barangRuangan->stok ?? 0) + $quantity;
                $barangRuangan->save();
            }

            // Generate transaction code
            $lastRecord = BarangMasuks::latest('id')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $kodeTrans = 'BM-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

            $barangMasuk = BarangMasuks::create([
                'kode_barang' => $kodeTrans,
                'id_barang' => $barang->id,
                'inventory_item_id' => null,
                'jumlah' => $quantity,
                'satuan_id' => $barang->satuan_id,
                'tanggal_masuk' => $tanggal,
                'keterangan' => $keterangan,
                'ruangan_id' => $ruanganId,
                'id_user' => $userId,
            ]);

            // Record movement ledger
            $this->movementService->record(
                $barang->id,
                null,
                'in',
                $quantity,
                $qtyBefore,
                $qtyAfter,
                'barang_masuk',
                $barangMasuk->id,
                $ruanganId,
                $userId,
                $keterangan,
                $tanggal
            );

            return $barangMasuk;
        });
    }

    /**
     * Process incoming stock for serialized items (single or multi-row)
     * $serialRows = [ ['serial_number' => 'SN1', 'quantity' => 1.0, 'ruangan_id' => 1], ... ]
     */
    public function processSerialized(int $barangId, array $serialRows, array $metaData, int $userId): array
    {
        return DB::transaction(function () use ($barangId, $serialRows, $metaData, $userId) {
            $barang = Barangs::lockForUpdate()->findOrFail($barangId);
            $barang->has_serial_number = true;
            $barang->save();

            $tanggal = $metaData['tanggal_masuk'] ?? Carbon::now()->toDateString();
            $keterangan = $metaData['keterangan'] ?? 'Penerimaan barang serial';
            $defaultRuanganId = $metaData['ruangan_id'] ?? null;

            $createdItems = [];
            $createdMasuks = [];

            // Check duplicate serial numbers in batch
            $serials = array_column($serialRows, 'serial_number');
            if (count($serials) !== count(array_unique($serials))) {
                throw new Exception('Terdapat duplikasi nomor seri dalam input yang dimasukkan.');
            }

            foreach ($serialRows as $row) {
                $serial = trim($row['serial_number']);
                $qty = isset($row['quantity']) && (float) $row['quantity'] > 0 ? (float) $row['quantity'] : 1.0;
                $ruanganId = !empty($row['ruangan_id']) ? $row['ruangan_id'] : $defaultRuanganId;

                // Check if existing item exists
                $existing = InventoryItem::lockForUpdate()->where('serial_number', $serial)->first();

                if ($existing) {
                    if ($existing->barang_id !== $barang->id) {
                        throw new Exception("Nomor seri {$serial} sudah digunakan untuk barang lain.");
                    }

                    // Existing cable / item top-up
                    $itemQtyBefore = (float) $existing->current_quantity;
                    $itemQtyAfter = $itemQtyBefore + $qty;

                    $existing->current_quantity = $itemQtyAfter;
                    $existing->status = 'available';
                    if ($ruanganId) {
                        $existing->ruangan_id = $ruanganId;
                    }
                    $existing->save();
                    $item = $existing;
                } else {
                    // Create new inventory item
                    $itemQtyBefore = 0.0;
                    $itemQtyAfter = $qty;

                    $item = InventoryItem::create([
                        'barang_id' => $barang->id,
                        'serial_number' => $serial,
                        'initial_quantity' => $qty,
                        'current_quantity' => $qty,
                        'satuan_id' => $barang->satuan_id,
                        'status' => 'available',
                        'ruangan_id' => $ruanganId,
                        'id_user' => $userId,
                        'tanggal_masuk' => $tanggal,
                        'keterangan' => $keterangan,
                    ]);
                }

                // Generate BM code
                $lastRecord = BarangMasuks::latest('id')->first();
                $lastId = $lastRecord ? $lastRecord->id : 0;
                $kodeTrans = 'BM-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

                $barangMasuk = BarangMasuks::create([
                    'kode_barang' => $kodeTrans,
                    'id_barang' => $barang->id,
                    'inventory_item_id' => $item->id,
                    'jumlah' => $qty,
                    'satuan_id' => $barang->satuan_id,
                    'tanggal_masuk' => $tanggal,
                    'keterangan' => $keterangan . " (SN: {$serial})",
                    'ruangan_id' => $ruanganId,
                    'id_user' => $userId,
                ]);

                // Record stock movement for this unit
                $this->movementService->record(
                    $barang->id,
                    $item->id,
                    'in',
                    $qty,
                    $itemQtyBefore,
                    $itemQtyAfter,
                    'barang_masuk',
                    $barangMasuk->id,
                    $ruanganId,
                    $userId,
                    $keterangan . " (SN: {$serial})",
                    $tanggal
                );

                $createdItems[] = $item;
                $createdMasuks[] = $barangMasuk;
            }

            // Sync master stock cache
            $this->inventoryService->syncMasterStock($barang->id);

            return [
                'items' => $createdItems,
                'barang_masuk' => $createdMasuks,
            ];
        });
    }
}
