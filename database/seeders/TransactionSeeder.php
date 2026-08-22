<?php

namespace Database\Seeders;

use App\Models\Barangs;
use App\Models\BarangKeluars;
use App\Models\BarangMasuks;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use App\Models\LocationHistory;
use App\Models\Peminjamans;
use App\Models\PeminjamanDetail;
use App\Models\Pengembalians;
use App\Models\PengembalianDetail;
use App\Models\Ruangans;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('is_admin', 1)->first() ?? User::first();
        $adminId = $admin ? $admin->id : 1;

        $userAndi = User::where('email', 'andi@invas.test')->first() ?? $admin;
        $userBudi = User::where('email', 'budi@invas.test')->first() ?? $admin;
        $userCandra = User::where('email', 'candra@invas.test')->first() ?? $admin;

        $roomTimeline = Ruangans::where('nama_ruangan', 'Kafe Timeline')->first();
        $roomBukadir = Ruangans::where('nama_ruangan', 'Kantor Bukadir & GWK (RV)')->first();
        $roomRiver = Ruangans::where('nama_ruangan', 'River Prawn')->first();

        $invService = app(InventoryService::class);

        // =========================================================================
        // 1. BARANG KELUAR (STOCK OUT: CABLE CONSUMPTION & MATERIAL USAGE)
        // =========================================================================
        // A. FO-ROLL-001 (Kabel Fiber Optik): 200m -> OUT 120m -> Sisa 80m
        $itemFo1 = InventoryItem::where('serial_number', 'FO-ROLL-001')->first();
        if ($itemFo1 && (float)$itemFo1->current_quantity == 200) {
            $barangFo = $itemFo1->barang;
            $qtyBefore = (float)$itemFo1->current_quantity;
            $qtyOut = 120.00;
            $qtyAfter = $qtyBefore - $qtyOut;

            $itemFo1->current_quantity = $qtyAfter;
            $itemFo1->save();

            $bkFo = BarangKeluars::firstOrCreate(
                ['kode_barang' => 'BK-0001'],
                [
                    'id_barang' => $barangFo->id,
                    'inventory_item_id' => $itemFo1->id,
                    'jumlah' => $qtyOut,
                    'satuan_id' => $barangFo->satuan_id,
                    'tanggal_keluar' => Carbon::now()->subDays(12)->toDateString(),
                    'keterangan' => 'Penarikan kabel instalasi backbone pelanggan baru PT Mandiri (SN: FO-ROLL-001)',
                    'ruangan_id' => $itemFo1->ruangan_id,
                    'id_user' => $userAndi->id,
                ]
            );

            StockMovement::firstOrCreate(
                [
                    'reference_type' => 'barang_keluar',
                    'reference_id' => $bkFo->id,
                ],
                [
                    'barang_id' => $barangFo->id,
                    'inventory_item_id' => $itemFo1->id,
                    'type' => 'out',
                    'quantity' => -$qtyOut,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $qtyAfter,
                    'ruangan_id' => $itemFo1->ruangan_id,
                    'user_id' => $userAndi->id,
                    'tanggal' => Carbon::now()->subDays(12),
                    'keterangan' => 'Penarikan kabel instalasi backbone PT Mandiri (SN: FO-ROLL-001)',
                ]
            );

            $invService->syncMasterStock($barangFo->id);
        }

        // B. DROP-001 (Dropcore): 500m -> OUT 150m -> Sisa 350m
        $itemDrop1 = InventoryItem::where('serial_number', 'DROP-001')->first();
        if ($itemDrop1 && (float)$itemDrop1->current_quantity == 500) {
            $barangDrop = $itemDrop1->barang;
            $qtyBefore = (float)$itemDrop1->current_quantity;
            $qtyOut = 150.00;
            $qtyAfter = $qtyBefore - $qtyOut;

            $itemDrop1->current_quantity = $qtyAfter;
            $itemDrop1->save();

            $bkDrop = BarangKeluars::firstOrCreate(
                ['kode_barang' => 'BK-0002'],
                [
                    'id_barang' => $barangDrop->id,
                    'inventory_item_id' => $itemDrop1->id,
                    'jumlah' => $qtyOut,
                    'satuan_id' => $barangDrop->satuan_id,
                    'tanggal_keluar' => Carbon::now()->subDays(8)->toDateString(),
                    'keterangan' => 'Penyambungan dropcore area perumahan Cluster Asri (SN: DROP-001)',
                    'ruangan_id' => $itemDrop1->ruangan_id,
                    'id_user' => $userBudi->id,
                ]
            );

            StockMovement::firstOrCreate(
                [
                    'reference_type' => 'barang_keluar',
                    'reference_id' => $bkDrop->id,
                ],
                [
                    'barang_id' => $barangDrop->id,
                    'inventory_item_id' => $itemDrop1->id,
                    'type' => 'out',
                    'quantity' => -$qtyOut,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $qtyAfter,
                    'ruangan_id' => $itemDrop1->ruangan_id,
                    'user_id' => $userBudi->id,
                    'tanggal' => Carbon::now()->subDays(8),
                    'keterangan' => 'Penyambungan dropcore area Cluster Asri (SN: DROP-001)',
                ]
            );

            $invService->syncMasterStock($barangDrop->id);
        }

        // C. Non-Serial Cable Tie: MAT-006 keluar 5 pack dari Kantor Bukadir
        $barangTie = Barangs::where('kode_barang', 'MAT-006')->first();
        if ($barangTie) {
            $brgR = BarangRuangans::where('barang_id', $barangTie->id)
                ->where('ruangan_id', $roomBukadir->id)
                ->first();

            if ($brgR && (float)$brgR->stok >= 5) {
                $qtyBefore = (float)$barangTie->stok;
                $brgR->stok = (float)$brgR->stok - 5;
                $brgR->save();

                $barangTie->stok = (float)$barangTie->stok - 5;
                $barangTie->save();

                $bkTie = BarangKeluars::firstOrCreate(
                    ['kode_barang' => 'BK-0003'],
                    [
                        'id_barang' => $barangTie->id,
                        'inventory_item_id' => null,
                        'jumlah' => 5,
                        'satuan_id' => $barangTie->satuan_id,
                        'tanggal_keluar' => Carbon::now()->subDays(5)->toDateString(),
                        'keterangan' => 'Material operasional instalasi kabel tie harian teknisi',
                        'ruangan_id' => $roomBukadir->id,
                        'id_user' => $userCandra->id,
                    ]
                );

                StockMovement::firstOrCreate(
                    [
                        'reference_type' => 'barang_keluar',
                        'reference_id' => $bkTie->id,
                    ],
                    [
                        'barang_id' => $barangTie->id,
                        'inventory_item_id' => null,
                        'type' => 'out',
                        'quantity' => -5,
                        'quantity_before' => $qtyBefore,
                        'quantity_after' => (float)$barangTie->stok,
                        'ruangan_id' => $roomBukadir->id,
                        'user_id' => $userCandra->id,
                        'tanggal' => Carbon::now()->subDays(5),
                        'keterangan' => 'Material operasional instalasi kabel tie harian',
                    ]
                );
            }
        }

        // =========================================================================
        // 2. PEMINJAMAN & PENGEMBALIAN (LOANS & RETURNS)
        // =========================================================================

        // A. TRANSAKSI 1: Active Loan (Fusion Splicer FS-90S-001 borrowed by Teknisi Budi)
        $itemFs1 = InventoryItem::where('serial_number', 'FS-90S-001')->first();
        if ($itemFs1) {
            $barangFs = $itemFs1->barang;
            $itemFs1->status = 'borrowed';
            $itemFs1->save();

            $loanFs1 = Peminjamans::firstOrCreate(
                ['kode_barang' => 'BP-0001'],
                [
                    'id_barang' => $barangFs->id,
                    'inventory_item_id' => $itemFs1->id,
                    'jumlah' => 1,
                    'satuan_id' => $barangFs->satuan_id,
                    'tanggal_pinjam' => Carbon::now()->subDays(3)->toDateString(),
                    'tanggal_kembali' => Carbon::now()->addDays(2)->toDateString(),
                    'nama_peminjam' => 'Teknisi Budi',
                    'status' => 'Sedang Dipinjam',
                    'ruangan_id' => $itemFs1->ruangan_id,
                    'id_user' => $adminId,
                ]
            );

            PeminjamanDetail::firstOrCreate(
                ['peminjaman_id' => $loanFs1->id, 'inventory_item_id' => $itemFs1->id],
                [
                    'barang_id' => $barangFs->id,
                    'quantity' => 1,
                    'returned_quantity' => 0,
                    'status' => 'borrowed',
                ]
            );

            StockMovement::firstOrCreate(
                ['reference_type' => 'peminjaman', 'reference_id' => $loanFs1->id],
                [
                    'barang_id' => $barangFs->id,
                    'inventory_item_id' => $itemFs1->id,
                    'type' => 'borrow',
                    'quantity' => -1,
                    'quantity_before' => 1,
                    'quantity_after' => 0,
                    'ruangan_id' => $itemFs1->ruangan_id,
                    'user_id' => $adminId,
                    'tanggal' => Carbon::now()->subDays(3),
                    'keterangan' => 'Dipinjam oleh Teknisi Budi untuk penyambungan kabel ODP FO (SN: FS-90S-001)',
                ]
            );
        }

        // B. TRANSAKSI 2: Returned Loan (Fusion Splicer FS-90S-002 borrowed and returned in good condition)
        $itemFs2 = InventoryItem::where('serial_number', 'FS-90S-002')->first();
        if ($itemFs2) {
            $barangFs = $itemFs2->barang;

            $loanFs2 = Peminjamans::firstOrCreate(
                ['kode_barang' => 'BP-0002'],
                [
                    'id_barang' => $barangFs->id,
                    'inventory_item_id' => $itemFs2->id,
                    'jumlah' => 1,
                    'satuan_id' => $barangFs->satuan_id,
                    'tanggal_pinjam' => Carbon::now()->subDays(10)->toDateString(),
                    'tanggal_kembali' => Carbon::now()->subDays(7)->toDateString(),
                    'nama_peminjam' => 'Teknisi Andi',
                    'status' => 'Sudah Dikembalikan',
                    'ruangan_id' => $itemFs2->ruangan_id,
                    'id_user' => $adminId,
                ]
            );

            $pDetail2 = PeminjamanDetail::firstOrCreate(
                ['peminjaman_id' => $loanFs2->id, 'inventory_item_id' => $itemFs2->id],
                [
                    'barang_id' => $barangFs->id,
                    'quantity' => 1,
                    'returned_quantity' => 1,
                    'status' => 'returned',
                ]
            );

            $returnFs2 = Pengembalians::firstOrCreate(
                ['kode_barang' => 'BB-0001'],
                [
                    'id_peminjam' => $loanFs2->id,
                    'id_barang' => $barangFs->id,
                    'inventory_item_id' => $itemFs2->id,
                    'jumlah' => 1,
                    'selisih' => 0,
                    'satuan_id' => $barangFs->satuan_id,
                    'tanggal_kembali' => Carbon::now()->subDays(7)->toDateString(),
                    'nama_peminjam' => 'Teknisi Andi',
                    'status' => 'Sudah Dikembalikan',
                    'kondisi' => 'Baik',
                    'ruangan_id' => $itemFs2->ruangan_id,
                    'id_user' => $adminId,
                ]
            );

            PengembalianDetail::firstOrCreate(
                ['pengembalian_id' => $returnFs2->id, 'inventory_item_id' => $itemFs2->id],
                [
                    'peminjaman_detail_id' => $pDetail2->id,
                    'barang_id' => $barangFs->id,
                    'quantity' => 1,
                    'selisih' => 0,
                    'kondisi' => 'Baik',
                    'keterangan' => 'Pengembalian tepat waktu, mesin normal dan bersih',
                ]
            );

            StockMovement::firstOrCreate(
                ['reference_type' => 'pengembalian', 'reference_id' => $returnFs2->id],
                [
                    'barang_id' => $barangFs->id,
                    'inventory_item_id' => $itemFs2->id,
                    'type' => 'return',
                    'quantity' => 1,
                    'quantity_before' => 0,
                    'quantity_after' => 1,
                    'ruangan_id' => $itemFs2->ruangan_id,
                    'user_id' => $adminId,
                    'tanggal' => Carbon::now()->subDays(7),
                    'keterangan' => 'Dikembalikan oleh Teknisi Andi (Kondisi: Baik, SN: FS-90S-002)',
                ]
            );
        }

        // C. TRANSAKSI 3: Overdue Loan (OPM-JW-002 borrowed 14 days ago, overdue by 7 days)
        $itemOpm2 = InventoryItem::where('serial_number', 'OPM-JW-002')->first();
        if ($itemOpm2) {
            $barangOpm = $itemOpm2->barang;
            $itemOpm2->status = 'borrowed';
            $itemOpm2->save();

            $loanOpm2 = Peminjamans::firstOrCreate(
                ['kode_barang' => 'BP-0003'],
                [
                    'id_barang' => $barangOpm->id,
                    'inventory_item_id' => $itemOpm2->id,
                    'jumlah' => 1,
                    'satuan_id' => $barangOpm->satuan_id,
                    'tanggal_pinjam' => Carbon::now()->subDays(14)->toDateString(),
                    'tanggal_kembali' => Carbon::now()->subDays(7)->toDateString(), // OVERDUE!
                    'nama_peminjam' => 'Teknisi Andi',
                    'status' => 'Sedang Dipinjam',
                    'ruangan_id' => $itemOpm2->ruangan_id,
                    'id_user' => $adminId,
                ]
            );

            PeminjamanDetail::firstOrCreate(
                ['peminjaman_id' => $loanOpm2->id, 'inventory_item_id' => $itemOpm2->id],
                [
                    'barang_id' => $barangOpm->id,
                    'quantity' => 1,
                    'returned_quantity' => 0,
                    'status' => 'borrowed',
                ]
            );

            StockMovement::firstOrCreate(
                ['reference_type' => 'peminjaman', 'reference_id' => $loanOpm2->id],
                [
                    'barang_id' => $barangOpm->id,
                    'inventory_item_id' => $itemOpm2->id,
                    'type' => 'borrow',
                    'quantity' => -1,
                    'quantity_before' => 1,
                    'quantity_after' => 0,
                    'ruangan_id' => $itemOpm2->ruangan_id,
                    'user_id' => $adminId,
                    'tanggal' => Carbon::now()->subDays(14),
                    'keterangan' => 'Dipinjam oleh Teknisi Andi untuk pengukuran redaman OLT (SN: OPM-JW-002)',
                ]
            );
        }

        // D. TRANSAKSI 4: Partial Return (Dropcore Cable DROP-002 300m -> borrowed 100m, returned 70m -> outstanding 30m)
        $itemDrop2 = InventoryItem::where('serial_number', 'DROP-002')->first();
        if ($itemDrop2) {
            $barangDrop = $itemDrop2->barang;

            $loanDrop2 = Peminjamans::firstOrCreate(
                ['kode_barang' => 'BP-0004'],
                [
                    'id_barang' => $barangDrop->id,
                    'inventory_item_id' => $itemDrop2->id,
                    'jumlah' => 100,
                    'satuan_id' => $barangDrop->satuan_id,
                    'tanggal_pinjam' => Carbon::now()->subDays(4)->toDateString(),
                    'tanggal_kembali' => Carbon::now()->addDays(3)->toDateString(),
                    'nama_peminjam' => 'Teknisi Candra',
                    'status' => 'Sedang Dipinjam',
                    'ruangan_id' => $itemDrop2->ruangan_id,
                    'id_user' => $adminId,
                ]
            );

            $pDetailDrop = PeminjamanDetail::firstOrCreate(
                ['peminjaman_id' => $loanDrop2->id, 'inventory_item_id' => $itemDrop2->id],
                [
                    'barang_id' => $barangDrop->id,
                    'quantity' => 100,
                    'returned_quantity' => 70,
                    'status' => 'partially_returned',
                ]
            );

            // Partial return 70m
            $returnDrop = Pengembalians::firstOrCreate(
                ['kode_barang' => 'BB-0002'],
                [
                    'id_peminjam' => $loanDrop2->id,
                    'id_barang' => $barangDrop->id,
                    'inventory_item_id' => $itemDrop2->id,
                    'jumlah' => 70,
                    'selisih' => 30, // 30m outstanding
                    'satuan_id' => $barangDrop->satuan_id,
                    'tanggal_kembali' => Carbon::now()->subDays(1)->toDateString(),
                    'nama_peminjam' => 'Teknisi Candra',
                    'status' => 'Sudah Dikembalikan',
                    'kondisi' => 'Baik',
                    'ruangan_id' => $itemDrop2->ruangan_id,
                    'id_user' => $adminId,
                ]
            );

            PengembalianDetail::firstOrCreate(
                ['pengembalian_id' => $returnDrop->id, 'inventory_item_id' => $itemDrop2->id],
                [
                    'peminjaman_detail_id' => $pDetailDrop->id,
                    'barang_id' => $barangDrop->id,
                    'quantity' => 70,
                    'selisih' => 30,
                    'kondisi' => 'Baik',
                    'keterangan' => 'Pengembalian tahap 1 (sisa 30 meter masih terpakai di lapangan)',
                ]
            );

            StockMovement::firstOrCreate(
                ['reference_type' => 'pengembalian', 'reference_id' => $returnDrop->id],
                [
                    'barang_id' => $barangDrop->id,
                    'inventory_item_id' => $itemDrop2->id,
                    'type' => 'return',
                    'quantity' => 70,
                    'quantity_before' => 200,
                    'quantity_after' => 270,
                    'ruangan_id' => $itemDrop2->ruangan_id,
                    'user_id' => $adminId,
                    'tanggal' => Carbon::now()->subDays(1),
                    'keterangan' => 'Pengembalian parsial 70 meter oleh Teknisi Candra (SN: DROP-002)',
                ]
            );
        }

        // =========================================================================
        // 3. LOCATION TRANSFERS & LOCATION HISTORY
        // =========================================================================
        // A. Laptop LPT-TP-001 transferred from Kafe Timeline to Kantor Bukadir & GWK (RV)
        $itemLpt = InventoryItem::where('serial_number', 'LPT-TP-001')->first();
        if ($itemLpt) {
            $itemLpt->ruangan_id = $roomBukadir->id;
            $itemLpt->save();

            LocationHistory::firstOrCreate(
                [
                    'inventory_item_id' => $itemLpt->id,
                    'from_ruangan_id' => $roomTimeline->id,
                    'to_ruangan_id' => $roomBukadir->id,
                ],
                [
                    'user_id' => $adminId,
                    'tanggal' => Carbon::now()->subDays(6),
                    'keterangan' => 'Dipindahkan untuk setup staging dan monitoring NOC kantor utama',
                ]
            );

            StockMovement::firstOrCreate(
                [
                    'inventory_item_id' => $itemLpt->id,
                    'type' => 'transfer',
                    'ruangan_id' => $roomBukadir->id,
                ],
                [
                    'barang_id' => $itemLpt->barang_id,
                    'type' => 'transfer',
                    'quantity' => 0,
                    'quantity_before' => 1,
                    'quantity_after' => 1,
                    'reference_type' => 'location_transfer',
                    'reference_id' => $itemLpt->id,
                    'user_id' => $adminId,
                    'tanggal' => Carbon::now()->subDays(6),
                    'keterangan' => "Pindah lokasi dari {$roomTimeline->nama_ruangan} ke {$roomBukadir->nama_ruangan} (SN: LPT-TP-001)",
                ]
            );
        }

        // B. Optical Power Meter OPM-JW-003 transferred from Kantor Bukadir to River Prawn
        $itemOpm3 = InventoryItem::where('serial_number', 'OPM-JW-003')->first();
        if ($itemOpm3) {
            $itemOpm3->ruangan_id = $roomRiver->id;
            $itemOpm3->save();

            LocationHistory::firstOrCreate(
                [
                    'inventory_item_id' => $itemOpm3->id,
                    'from_ruangan_id' => $roomBukadir->id,
                    'to_ruangan_id' => $roomRiver->id,
                ],
                [
                    'user_id' => $adminId,
                    'tanggal' => Carbon::now()->subDays(4),
                    'keterangan' => 'Alokasi alat ukur standby untuk teknisi gudang River Prawn',
                ]
            );

            StockMovement::firstOrCreate(
                [
                    'inventory_item_id' => $itemOpm3->id,
                    'type' => 'transfer',
                    'ruangan_id' => $roomRiver->id,
                ],
                [
                    'barang_id' => $itemOpm3->barang_id,
                    'type' => 'transfer',
                    'quantity' => 0,
                    'quantity_before' => 1,
                    'quantity_after' => 1,
                    'reference_type' => 'location_transfer',
                    'reference_id' => $itemOpm3->id,
                    'user_id' => $adminId,
                    'tanggal' => Carbon::now()->subDays(4),
                    'keterangan' => "Pindah lokasi dari {$roomBukadir->nama_ruangan} ke {$roomRiver->nama_ruangan} (SN: OPM-JW-003)",
                ]
            );
        }

        // =========================================================================
        // 4. STOCK ADJUSTMENT (STOCK OPNAME CORRECTION)
        // =========================================================================
        // Kabel Fiber Optik FO-ROLL-003 (River Prawn): System 275m -> Physical 270m (Delta -5m)
        $itemFo3 = InventoryItem::where('serial_number', 'FO-ROLL-003')->first();
        if ($itemFo3 && (float)$itemFo3->current_quantity == 275) {
            $barangFo = $itemFo3->barang;
            $qtyBefore = (float)$itemFo3->current_quantity;
            $actualQty = 270.00;
            $delta = $actualQty - $qtyBefore;

            $itemFo3->current_quantity = $actualQty;
            $itemFo3->save();

            StockMovement::firstOrCreate(
                [
                    'inventory_item_id' => $itemFo3->id,
                    'type' => 'adjustment',
                ],
                [
                    'barang_id' => $barangFo->id,
                    'type' => 'adjustment',
                    'quantity' => $delta,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $actualQty,
                    'reference_type' => 'stock_opname',
                    'reference_id' => $itemFo3->id,
                    'ruangan_id' => $itemFo3->ruangan_id,
                    'user_id' => $adminId,
                    'tanggal' => Carbon::now()->subDays(2),
                    'keterangan' => 'Selisih hasil pengecekan fisik stock opname akhir bulan (SN: FO-ROLL-003)',
                ]
            );

            $invService->syncMasterStock($barangFo->id);
        }
    }
}
