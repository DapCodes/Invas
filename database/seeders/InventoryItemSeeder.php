<?php

namespace Database\Seeders;

use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\Ruangans;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\User;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('is_admin', 1)->first() ?? User::first();
        $adminId = $admin ? $admin->id : 1;

        $roomTimeline = Ruangans::where('nama_ruangan', 'Kafe Timeline')->first();
        $roomBukadir = Ruangans::where('nama_ruangan', 'Kantor Bukadir & GWK (RV)')->first();
        $roomRiver = Ruangans::where('nama_ruangan', 'River Prawn')->first();

        $invService = app(InventoryService::class);
        $startDate = Carbon::now()->subDays(30);

        // =========================================================================
        // 1. NON-SERIALIZED ITEMS (DISTRIBUTE STOCK INTO BARANG_RUANGANS)
        // =========================================================================
        $nonSerialAllocations = [
            'MAT-001' => [ // RJ45: 500 pcs
                ['room' => $roomTimeline, 'stok' => 200],
                ['room' => $roomBukadir, 'stok' => 200],
                ['room' => $roomRiver, 'stok' => 100],
            ],
            'MAT-002' => [ // Fast Connector SC/UPC: 300 pcs
                ['room' => $roomTimeline, 'stok' => 100],
                ['room' => $roomBukadir, 'stok' => 120],
                ['room' => $roomRiver, 'stok' => 80],
            ],
            'MAT-003' => [ // Fast Connector SC/APC: 250 pcs
                ['room' => $roomTimeline, 'stok' => 80],
                ['room' => $roomBukadir, 'stok' => 100],
                ['room' => $roomRiver, 'stok' => 70],
            ],
            'MAT-004' => [ // Sleeve 60mm: 800 pcs
                ['room' => $roomTimeline, 'stok' => 300],
                ['room' => $roomBukadir, 'stok' => 300],
                ['room' => $roomRiver, 'stok' => 200],
            ],
            'MAT-005' => [ // Adapter SC: 180 pcs
                ['room' => $roomTimeline, 'stok' => 60],
                ['room' => $roomBukadir, 'stok' => 70],
                ['room' => $roomRiver, 'stok' => 50],
            ],
            'MAT-006' => [ // Cable Tie: 45 pack
                ['room' => $roomTimeline, 'stok' => 15],
                ['room' => $roomBukadir, 'stok' => 20],
                ['room' => $roomRiver, 'stok' => 10],
            ],
            'MAT-007' => [ // Insulation Tape: 70 roll
                ['room' => $roomTimeline, 'stok' => 25],
                ['room' => $roomBukadir, 'stok' => 25],
                ['room' => $roomRiver, 'stok' => 20],
            ],
            'MAT-008' => [ // OTB 8 Core: 35 pcs
                ['room' => $roomTimeline, 'stok' => 10],
                ['room' => $roomBukadir, 'stok' => 15],
                ['room' => $roomRiver, 'stok' => 10],
            ],
            'KBL-005' => [ // Patch Cord 3M: 120 pcs
                ['room' => $roomTimeline, 'stok' => 40],
                ['room' => $roomBukadir, 'stok' => 50],
                ['room' => $roomRiver, 'stok' => 30],
            ],
            'KBL-006' => [ // Patch Cord 5M: 85 pcs
                ['room' => $roomTimeline, 'stok' => 30],
                ['room' => $roomBukadir, 'stok' => 35],
                ['room' => $roomRiver, 'stok' => 20],
            ],
            'KBL-007' => [ // Patch Cord LAN: 150 pcs
                ['room' => $roomTimeline, 'stok' => 50],
                ['room' => $roomBukadir, 'stok' => 60],
                ['room' => $roomRiver, 'stok' => 40],
            ],
            'KBL-008' => [ // Kabel Power: 60 pcs
                ['room' => $roomTimeline, 'stok' => 20],
                ['room' => $roomBukadir, 'stok' => 25],
                ['room' => $roomRiver, 'stok' => 15],
            ],
            'FO-008' => [ // Cleaning Kit: 25 set
                ['room' => $roomTimeline, 'stok' => 8],
                ['room' => $roomBukadir, 'stok' => 10],
                ['room' => $roomRiver, 'stok' => 7],
            ],
            'TOL-005' => [ // Tang Potong: 18 unit
                ['room' => $roomTimeline, 'stok' => 6],
                ['room' => $roomBukadir, 'stok' => 7],
                ['room' => $roomRiver, 'stok' => 5],
            ],
            'TOL-006' => [ // Obeng Set: 12 set
                ['room' => $roomTimeline, 'stok' => 4],
                ['room' => $roomBukadir, 'stok' => 5],
                ['room' => $roomRiver, 'stok' => 3],
            ],
            'OFF-004' => [ // Kursi Kerja: 45 pcs
                ['room' => $roomTimeline, 'stok' => 20],
                ['room' => $roomBukadir, 'stok' => 15],
                ['room' => $roomRiver, 'stok' => 10],
            ],
            'OFF-005' => [ // Meja Kerja: 8 unit
                ['room' => $roomTimeline, 'stok' => 3],
                ['room' => $roomBukadir, 'stok' => 3],
                ['room' => $roomRiver, 'stok' => 2],
            ],
            'OFF-006' => [ // Rak Server: 6 unit
                ['room' => $roomTimeline, 'stok' => 2],
                ['room' => $roomBukadir, 'stok' => 2],
                ['room' => $roomRiver, 'stok' => 2],
            ],
        ];

        foreach ($nonSerialAllocations as $kode => $allocs) {
            $barang = Barangs::where('kode_barang', $kode)->first();
            if (!$barang) continue;

            $totalMaster = 0;
            foreach ($allocs as $alloc) {
                if (!$alloc['room']) continue;

                BarangRuangans::firstOrCreate(
                    [
                        'barang_id' => $barang->id,
                        'ruangan_id' => $alloc['room']->id,
                    ],
                    [
                        'stok' => $alloc['stok'],
                    ]
                );

                $totalMaster += $alloc['stok'];

                // Record initial movement if not exists
                $exists = StockMovement::where('barang_id', $barang->id)
                    ->where('ruangan_id', $alloc['room']->id)
                    ->where('type', 'initial')
                    ->exists();

                if (!$exists) {
                    StockMovement::create([
                        'barang_id' => $barang->id,
                        'inventory_item_id' => null,
                        'type' => 'initial',
                        'quantity' => $alloc['stok'],
                        'quantity_before' => 0,
                        'quantity_after' => $alloc['stok'],
                        'reference_type' => 'initial_seed',
                        'reference_id' => $barang->id,
                        'ruangan_id' => $alloc['room']->id,
                        'user_id' => $adminId,
                        'tanggal' => $startDate,
                        'keterangan' => "Saldo awal non-serial di {$alloc['room']->nama_ruangan}",
                    ]);
                }
            }

            $barang->stok = $totalMaster;
            $barang->save();
        }

        // =========================================================================
        // 2. SERIALIZED DISCRETE UNITS (SINGLE PIECES QTY = 1)
        // =========================================================================
        $serializedDiscrete = [
            'NET-001' => [ // MikroTik RB750Gr3
                ['sn' => 'MTK-750-001', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'MTK-750-002', 'room' => $roomTimeline, 'status' => 'available'],
                ['sn' => 'MTK-750-003', 'room' => $roomRiver, 'status' => 'available'],
                ['sn' => 'MTK-750-004', 'room' => $roomBukadir, 'status' => 'available'],
            ],
            'NET-002' => [ // MikroTik CCR1009
                ['sn' => 'CCR-1009-001', 'room' => $roomBukadir, 'status' => 'in_use'],
                ['sn' => 'CCR-1009-002', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'NET-003' => [ // TP-Link AX3000
                ['sn' => 'TPL-AX3-001', 'room' => $roomTimeline, 'status' => 'available'],
                ['sn' => 'TPL-AX3-002', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'TPL-AX3-003', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'NET-004' => [ // Switch 8 Port
                ['sn' => 'SW8-001', 'room' => $roomTimeline, 'status' => 'available'],
                ['sn' => 'SW8-002', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'SW8-003', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'NET-005' => [ // Switch 16 Port
                ['sn' => 'SW16-001', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'SW16-002', 'room' => $roomTimeline, 'status' => 'available'],
            ],
            'NET-006' => [ // Switch 24 Port PoE
                ['sn' => 'CSC-24P-001', 'room' => $roomBukadir, 'status' => 'in_use'],
                ['sn' => 'CSC-24P-002', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'NET-007' => [ // UniFi AP Outdoor
                ['sn' => 'UAP-OUT-001', 'room' => $roomTimeline, 'status' => 'in_use'],
                ['sn' => 'UAP-OUT-002', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'UAP-OUT-003', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'NET-008' => [ // ZTE ONT F609
                ['sn' => 'ZTE-F609-001', 'room' => $roomTimeline, 'status' => 'available'],
                ['sn' => 'ZTE-F609-002', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'ZTE-F609-003', 'room' => $roomRiver, 'status' => 'available'],
                ['sn' => 'ZTE-F609-004', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'ZTE-F609-005', 'room' => $roomRiver, 'status' => 'damaged'],
            ],
            'NET-009' => [ // Huawei GPON OLT
                ['sn' => 'HWI-OLT-001', 'room' => $roomBukadir, 'status' => 'in_use'],
            ],
            'NET-010' => [ // Media Converter
                ['sn' => 'MC-GB-001', 'room' => $roomTimeline, 'status' => 'available'],
                ['sn' => 'MC-GB-002', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'MC-GB-003', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'NET-011' => [ // PoE Injector
                ['sn' => 'POE-48V-001', 'room' => $roomTimeline, 'status' => 'available'],
                ['sn' => 'POE-48V-002', 'room' => $roomBukadir, 'status' => 'available'],
            ],
            'NET-012' => [ // Network Tester
                ['sn' => 'TST-LAN-001', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'TST-LAN-002', 'room' => $roomRiver, 'status' => 'available'],
            ],

            // B. Fiber Optic Equipment
            'FO-001' => [ // Fusion Splicer Fujikura 90S+
                ['sn' => 'FS-90S-001', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'FS-90S-002', 'room' => $roomRiver, 'status' => 'available'],
                ['sn' => 'FS-90S-003', 'room' => $roomTimeline, 'status' => 'available'],
            ],
            'FO-002' => [ // OTDR Grandway
                ['sn' => 'OTDR-GW-001', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'OTDR-GW-002', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'FO-003' => [ // OPM Joinwit
                ['sn' => 'OPM-JW-001', 'room' => $roomTimeline, 'status' => 'available'],
                ['sn' => 'OPM-JW-002', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'OPM-JW-003', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'FO-004' => [ // VFL Komshine 30mW
                ['sn' => 'VFL-30M-001', 'room' => $roomTimeline, 'status' => 'available'],
                ['sn' => 'VFL-30M-002', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'VFL-30M-003', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'FO-005' => [ // Fiber Cleaver Sumitomo
                ['sn' => 'FC-6S-001', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'FC-6S-002', 'room' => $roomTimeline, 'status' => 'available'],
            ],
            'FO-006' => [ // Fiber Stripper Miller
                ['sn' => 'STR-CFS-001', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'STR-CFS-002', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'FO-007' => [ // Fiber Identifier Tribrer
                ['sn' => 'OFI-3-001', 'room' => $roomBukadir, 'status' => 'available'],
            ],

            // E. Tools
            'TOL-001' => [ // Tang Crimping
                ['sn' => 'CRM-PSK-001', 'room' => $roomTimeline, 'status' => 'available'],
                ['sn' => 'CRM-PSK-002', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'CRM-PSK-003', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'TOL-002' => [ // Toolkit ISP
                ['sn' => 'KIT-ISP-001', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'KIT-ISP-002', 'room' => $roomTimeline, 'status' => 'available'],
                ['sn' => 'KIT-ISP-003', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'TOL-003' => [ // Multimeter Sanwa
                ['sn' => 'DMM-SNW-001', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'DMM-SNW-002', 'room' => $roomTimeline, 'status' => 'available'],
            ],
            'TOL-004' => [ // Bor Baterai Bosch
                ['sn' => 'BOR-BSH-001', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'BOR-BSH-002', 'room' => $roomRiver, 'status' => 'available'],
            ],

            // F. Support
            'OFF-001' => [ // Laptop ThinkPad T14
                ['sn' => 'LPT-TP-001', 'room' => $roomBukadir, 'status' => 'available'],
                ['sn' => 'LPT-TP-002', 'room' => $roomTimeline, 'status' => 'available'],
                ['sn' => 'LPT-TP-003', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'OFF-002' => [ // Monitor LG 24 Inch
                ['sn' => 'MON-LG-001', 'room' => $roomBukadir, 'status' => 'in_use'],
                ['sn' => 'MON-LG-002', 'room' => $roomTimeline, 'status' => 'in_use'],
                ['sn' => 'MON-LG-003', 'room' => $roomRiver, 'status' => 'available'],
            ],
            'OFF-003' => [ // UPS Online APC 1000VA
                ['sn' => 'UPS-APC-001', 'room' => $roomBukadir, 'status' => 'in_use'],
                ['sn' => 'UPS-APC-002', 'room' => $roomRiver, 'status' => 'in_use'],
            ],
        ];

        foreach ($serializedDiscrete as $kode => $units) {
            $barang = Barangs::where('kode_barang', $kode)->first();
            if (!$barang) continue;

            foreach ($units as $u) {
                $item = InventoryItem::firstOrCreate(
                    ['serial_number' => $u['sn']],
                    [
                        'barang_id' => $barang->id,
                        'initial_quantity' => 1,
                        'current_quantity' => 1,
                        'satuan_id' => $barang->satuan_id,
                        'status' => $u['status'],
                        'ruangan_id' => $u['room']?->id,
                        'id_user' => $adminId,
                        'tanggal_masuk' => $startDate->toDateString(),
                        'keterangan' => 'Registrasi unit serial awal ISP',
                    ]
                );

                $movementExists = StockMovement::where('inventory_item_id', $item->id)
                    ->where('type', 'initial')
                    ->exists();

                if (!$movementExists) {
                    StockMovement::create([
                        'barang_id' => $barang->id,
                        'inventory_item_id' => $item->id,
                        'type' => 'initial',
                        'quantity' => 1,
                        'quantity_before' => 0,
                        'quantity_after' => 1,
                        'reference_type' => 'initial_seed',
                        'reference_id' => $item->id,
                        'ruangan_id' => $u['room']?->id,
                        'user_id' => $adminId,
                        'tanggal' => $startDate,
                        'keterangan' => "Saldo awal unit serial {$u['sn']}",
                    ]);
                }
            }

            $invService->syncMasterStock($barang->id);
        }

        // =========================================================================
        // 3. SERIALIZED CONTINUOUS QUANTITY (KABEL FIBER OPTIK & LAN ROLLS)
        // =========================================================================
        $cableRolls = [
            'KBL-001' => [ // Kabel FO ADSS 12 Core
                ['sn' => 'FO-ROLL-001', 'qty' => 200.00, 'room' => $roomTimeline],
                ['sn' => 'FO-ROLL-002', 'qty' => 400.00, 'room' => $roomBukadir],
                ['sn' => 'FO-ROLL-003', 'qty' => 275.00, 'room' => $roomRiver],
                ['sn' => 'FO-ROLL-004', 'qty' => 150.00, 'room' => $roomBukadir],
            ],
            'KBL-002' => [ // Kabel Dropcore FO 1 Core
                ['sn' => 'DROP-001', 'qty' => 500.00, 'room' => $roomTimeline],
                ['sn' => 'DROP-002', 'qty' => 300.00, 'room' => $roomBukadir],
                ['sn' => 'DROP-003', 'qty' => 450.00, 'room' => $roomRiver],
                ['sn' => 'DROP-004', 'qty' => 250.00, 'room' => $roomBukadir],
            ],
            'KBL-003' => [ // Kabel LAN Cat6
                ['sn' => 'LAN6-001', 'qty' => 305.00, 'room' => $roomTimeline],
                ['sn' => 'LAN6-002', 'qty' => 280.00, 'room' => $roomBukadir],
                ['sn' => 'LAN6-003', 'qty' => 150.00, 'room' => $roomRiver],
            ],
            'KBL-004' => [ // Kabel LAN FTP Cat5e Outdoor
                ['sn' => 'LAN5-OUT-001', 'qty' => 305.00, 'room' => $roomBukadir],
                ['sn' => 'LAN5-OUT-002', 'qty' => 220.00, 'room' => $roomRiver],
            ],
        ];

        foreach ($cableRolls as $kode => $rolls) {
            $barang = Barangs::where('kode_barang', $kode)->first();
            if (!$barang) continue;

            foreach ($rolls as $r) {
                $item = InventoryItem::firstOrCreate(
                    ['serial_number' => $r['sn']],
                    [
                        'barang_id' => $barang->id,
                        'initial_quantity' => $r['qty'],
                        'current_quantity' => $r['qty'],
                        'satuan_id' => $barang->satuan_id,
                        'status' => 'available',
                        'ruangan_id' => $r['room']?->id,
                        'id_user' => $adminId,
                        'tanggal_masuk' => $startDate->toDateString(),
                        'keterangan' => "Gulungan kabel drum {$r['sn']} ({$r['qty']} meter)",
                    ]
                );

                $movementExists = StockMovement::where('inventory_item_id', $item->id)
                    ->where('type', 'initial')
                    ->exists();

                if (!$movementExists) {
                    StockMovement::create([
                        'barang_id' => $barang->id,
                        'inventory_item_id' => $item->id,
                        'type' => 'initial',
                        'quantity' => $r['qty'],
                        'quantity_before' => 0,
                        'quantity_after' => $r['qty'],
                        'reference_type' => 'initial_seed',
                        'reference_id' => $item->id,
                        'ruangan_id' => $r['room']?->id,
                        'user_id' => $adminId,
                        'tanggal' => $startDate,
                        'keterangan' => "Saldo awal gulungan {$r['sn']} ({$r['qty']} meter)",
                    ]);
                }
            }

            $invService->syncMasterStock($barang->id);
        }
    }
}
