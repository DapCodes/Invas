<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Barangs;
use App\Models\Unit;
use App\Models\User;
use App\Models\Ruangans;
use App\Models\StockMovement;
use App\Services\StockInService;

$stockInService = app(StockInService::class);
$unitMeter = Unit::where('symbol', 'meter')->first();
$unitPcs = Unit::where('symbol', 'pcs')->first();
$user = User::first();
$room = Ruangans::first();

echo "--- TESTING STEP 3 & STEP 4 ---\n";

// 1. Kursi Non-Serial
$kursi = Barangs::create([
    'kode_barang' => 'KRS-TEST-01',
    'nama' => 'Kursi Tamu Eksklusif',
    'merek' => 'Chitose',
    'satuan_id' => $unitPcs->id,
    'has_serial_number' => false,
    'is_active' => true,
    'stok' => 0,
    'id_user' => $user->id,
]);

$stockInService->processNonSerialized([
    'barang_id' => $kursi->id,
    'jumlah' => 100,
    'ruangan_id' => $room->id,
    'keterangan' => 'Stok awal test non-serial',
], $user->id);

// 2. Kabel Serialized
$kabel = Barangs::create([
    'kode_barang' => 'KFO-TEST-01',
    'nama' => 'Kabel Fiber Optik Drop Core',
    'merek' => 'Belden',
    'satuan_id' => $unitMeter->id,
    'has_serial_number' => true,
    'is_active' => true,
    'stok' => 0,
    'id_user' => $user->id,
]);

$stockInService->processSerialized($kabel->id, [
    ['serial_number' => 'FO-TEST-001', 'quantity' => 200, 'ruangan_id' => $room->id],
    ['serial_number' => 'FO-TEST-002', 'quantity' => 400, 'ruangan_id' => $room->id],
    ['serial_number' => 'FO-TEST-003', 'quantity' => 300, 'ruangan_id' => $room->id],
], ['keterangan' => 'Penerimaan 3 drum kabel FO'], $user->id);

$kursi->refresh();
$kabel->refresh();

echo "Kursi Master Stok: {$kursi->stok} {$kursi->unit->symbol}\n";
echo "Kabel Master Stok: {$kabel->stok} {$kabel->unit->symbol}\n";
echo "Kabel Total Serials: " . $kabel->inventoryItems()->count() . "\n";
echo "Total Stock Movements in system: " . StockMovement::count() . "\n";

foreach ($kabel->inventoryItems as $item) {
    echo " -> Serial {$item->serial_number}: Qty {$item->current_quantity} {$item->barang->unit->symbol} (Status: {$item->status})\n";
}

echo "TEST SUCCESSFUL!\n";
