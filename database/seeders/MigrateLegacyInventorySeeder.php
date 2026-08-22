<?php

namespace Database\Seeders;

use App\Models\Barangs;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\Ruangans;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateLegacyInventorySeeder extends Seeder
{
    /**
     * Run the database seeds to migrate legacy data cleanly.
     */
    public function run(): void
    {
        $defaultUnitPcs = Unit::where('symbol', 'pcs')->first();
        $defaultUnitItem = Unit::where('symbol', 'unit')->first() ?? $defaultUnitPcs;
        $defaultRuangan = Ruangans::first();

        $barangs = Barangs::all();

        foreach ($barangs as $barang) {
            DB::transaction(function () use ($barang, $defaultUnitPcs, $defaultUnitItem, $defaultRuangan) {
                // 1. Set default unit if empty
                if (!$barang->satuan_id) {
                    $barang->satuan_id = $defaultUnitPcs ? $defaultUnitPcs->id : null;
                }

                // 2. Check if legacy serial_number exists
                if (!empty($barang->serial_number)) {
                    $barang->has_serial_number = true;
                    $barang->save();

                    $existingItem = InventoryItem::where('serial_number', $barang->serial_number)->first();
                    if (!$existingItem) {
                        $qty = $barang->stok > 0 ? $barang->stok : 1;
                        $inventoryItem = InventoryItem::create([
                            'barang_id' => $barang->id,
                            'serial_number' => $barang->serial_number,
                            'initial_quantity' => $qty,
                            'current_quantity' => $qty,
                            'satuan_id' => $barang->satuan_id ?? ($defaultUnitItem ? $defaultUnitItem->id : null),
                            'status' => 'available',
                            'ruangan_id' => $defaultRuangan ? $defaultRuangan->id : null,
                            'id_user' => $barang->id_user,
                            'tanggal_masuk' => $barang->created_at ? $barang->created_at->toDateString() : Carbon::now()->toDateString(),
                            'keterangan' => 'Migrasi otomatis data legacy serial number',
                        ]);

                        StockMovement::create([
                            'barang_id' => $barang->id,
                            'inventory_item_id' => $inventoryItem->id,
                            'type' => 'initial',
                            'quantity' => $qty,
                            'quantity_before' => 0,
                            'quantity_after' => $qty,
                            'reference_type' => 'legacy_migration',
                            'reference_id' => $barang->id,
                            'ruangan_id' => $defaultRuangan ? $defaultRuangan->id : null,
                            'user_id' => $barang->id_user,
                            'tanggal' => Carbon::now(),
                            'keterangan' => 'Saldo awal migrasi data legacy',
                        ]);
                    }
                } else {
                    $barang->has_serial_number = false;
                    $barang->save();

                    // Create initial movement record for non-serial stock if it has stock and no movements exist
                    $hasMovement = StockMovement::where('barang_id', $barang->id)->exists();
                    if (!$hasMovement && $barang->stok > 0) {
                        StockMovement::create([
                            'barang_id' => $barang->id,
                            'inventory_item_id' => null,
                            'type' => 'initial',
                            'quantity' => $barang->stok,
                            'quantity_before' => 0,
                            'quantity_after' => $barang->stok,
                            'reference_type' => 'legacy_migration',
                            'reference_id' => $barang->id,
                            'ruangan_id' => $defaultRuangan ? $defaultRuangan->id : null,
                            'user_id' => $barang->id_user,
                            'tanggal' => Carbon::now(),
                            'keterangan' => 'Saldo awal non-serial migrasi data legacy',
                        ]);
                    }
                }
            });
        }
    }
}
