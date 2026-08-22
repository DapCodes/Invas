<?php

namespace Database\Seeders;

use App\Models\Barangs;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('is_admin', 1)->first() ?? User::first();
        $adminId = $admin ? $admin->id : 1;

        // Units
        $unitPcs = Unit::where('symbol', 'pcs')->first();
        $unitUnit = Unit::where('symbol', 'unit')->first();
        $unitMeter = Unit::where('symbol', 'meter')->first();
        $unitRoll = Unit::where('symbol', 'roll')->first();
        $unitBox = Unit::where('symbol', 'box')->first();
        $unitPack = Unit::where('symbol', 'pack')->first();
        $unitSet = Unit::where('symbol', 'set')->first();

        // Vendors
        $vFiber = Vendor::where('name', 'like', '%Fiber Teknologi%')->first();
        $vNetwork = Vendor::where('name', 'like', '%Network Solution%')->first();
        $vSinar = Vendor::where('name', 'like', '%Sinar Telekomunikasi%')->first();
        $vMitra = Vendor::where('name', 'like', '%Mitra Infrastruktur%')->first();
        $vSolusi = Vendor::where('name', 'like', '%Solusi Jaringan%')->first();

        $barangs = [
            // ==========================================
            // A. PERANGKAT NETWORK
            // ==========================================
            [
                'kode_barang' => 'NET-001',
                'nama' => 'Router MikroTik RB750Gr3 (hEX)',
                'merek' => 'MikroTik',
                'deskripsi' => 'Router 5 port Gigabit Ethernet untuk gateway & manajemen bandwidth pelanggan SOHO',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vNetwork?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'NET-002',
                'nama' => 'Router MikroTik CCR1009-7G-1C-1S+',
                'merek' => 'MikroTik',
                'deskripsi' => 'Cloud Core Router 9 core untuk core routing dan BGP peering network ISP',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vNetwork?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'NET-003',
                'nama' => 'Router WiFi Dual Band Gigabit AX3000',
                'merek' => 'TP-Link',
                'deskripsi' => 'Wireless Router Wi-Fi 6 untuk instalasi pelanggan premium',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vNetwork?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'NET-004',
                'nama' => 'Switch 8 Port Gigabit Unmanaged',
                'merek' => 'D-Link',
                'deskripsi' => 'Desktop switch 8 port 10/100/1000 Mbps untuk distribusi lokal',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vSolusi?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'NET-005',
                'nama' => 'Switch 16 Port Gigabit Smart Managed',
                'merek' => 'Ruijie Reyee',
                'deskripsi' => 'Switch 16 port dengan fitur VLAN & cloud management',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vNetwork?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'NET-006',
                'nama' => 'Switch 24 Port Gigabit PoE+ Managed',
                'merek' => 'Cisco Catalyst',
                'deskripsi' => 'Switch rackmount 24 port PoE+ dengan 4 SFP uplink',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vNetwork?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'NET-007',
                'nama' => 'Wireless Access Point Outdoor UniFi',
                'merek' => 'Ubiquiti',
                'deskripsi' => 'Access point outdoor AC Mesh untuk coverage area publik dan kafe',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vNetwork?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'NET-008',
                'nama' => 'GPON ONT / ONU 1GE+3FE+WiFi F609',
                'merek' => 'ZTE',
                'deskripsi' => 'Optical Network Terminal untuk terminal fiber optik pelanggan rumahan',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vSinar?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'NET-009',
                'nama' => 'GPON OLT 8 Port Standalone Pizza-Box',
                'merek' => 'Huawei',
                'deskripsi' => 'Optical Line Terminal 8 PON port kapasitas hingga 1024 ONT',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vSinar?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'NET-010',
                'nama' => 'Media Converter Gigabit Fiber HTB-GS-03',
                'merek' => 'Netlink',
                'deskripsi' => 'Sepasang media converter Single Mode WDM 20KM 10/100/1000M',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'NET-011',
                'nama' => 'Gigabit PoE Injector 48V 24W',
                'merek' => 'Ubiquiti',
                'deskripsi' => 'Power over Ethernet adapter pasif untuk wireless radio dan IP Camera',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vNetwork?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'NET-012',
                'nama' => 'Network Cable Tester & Tracker NF-8209',
                'merek' => 'Noyafa',
                'deskripsi' => 'Alat pelacak kabel LAN, cek kontinuitas RJ45, dan tes tegangan PoE',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vMitra?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],

            // ==========================================
            // B. PERALATAN TEKNISI FIBER OPTIK
            // ==========================================
            [
                'kode_barang' => 'FO-001',
                'nama' => 'Fusion Splicer Core Alignment 90S+',
                'merek' => 'Fujikura',
                'deskripsi' => 'Mesin penyambung kabel fiber optik presisi tinggi dengan waktu sambung 7 detik',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vMitra?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'FO-002',
                'nama' => 'Optical Time Domain Reflectometer (OTDR) 1310/1550nm',
                'merek' => 'Grandway',
                'deskripsi' => 'Alat ukur visualisasi jalur fiber optik, mendeteksi redaman dan titik putus kabel',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vMitra?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'FO-003',
                'nama' => 'Optical Power Meter (OPM) & VFL 2in1 JW3208',
                'merek' => 'Joinwit',
                'deskripsi' => 'Pengukur kekuatan sinyal optik dBm lengkap dengan laser merah VFL',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vMitra?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'FO-004',
                'nama' => 'Visual Fault Locator (VFL) Laser Merah 30mW',
                'merek' => 'Komshine',
                'deskripsi' => 'Senter laser serat optik jangkauan hingga 30 km untuk penelusuran core',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'FO-005',
                'nama' => 'Precision Fiber Cleaver FC-6S',
                'merek' => 'Sumitomo',
                'deskripsi' => 'Pemotong serat optik sudut 90 derajat presisi sebelum proses splicing',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vMitra?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'FO-006',
                'nama' => 'Fiber Stripper CFS-3 Tri-Hole',
                'merek' => 'Miller',
                'deskripsi' => 'Tang pengupas lapisan jaket luar, tube, dan coating serat optik 250um',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'FO-007',
                'nama' => 'Optical Fiber Identifier Live Line OFI-3',
                'merek' => 'Tribrer',
                'deskripsi' => 'Detektor keberadaan arah dan modulasi trafik sinyal optik aktif tanpa memutus jalur',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vMitra?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'FO-008',
                'nama' => 'Fiber Optic Cleaning Kit Pen & Cassette',
                'merek' => 'NTT-AT',
                'deskripsi' => 'Paket pembersih ferrule connector SC/LC dan alkohol swab pembersih core',
                'satuan_id' => $unitSet->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => false,
                'stok' => 25,
            ],

            // ==========================================
            // C. KABEL (SERIALIZED + CONTINUOUS QUANTITY)
            // ==========================================
            [
                'kode_barang' => 'KBL-001',
                'nama' => 'Kabel Fiber Optik ADSS 12 Core Single Mode',
                'merek' => 'Belden',
                'deskripsi' => 'Kabel udara aerial fiber optik 12 core outdoor kapasitas backbone',
                'satuan_id' => $unitMeter->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'KBL-002',
                'nama' => 'Kabel Dropcore FO 1 Core 3 Steel Wire G657A',
                'merek' => 'FiberHome',
                'deskripsi' => 'Kabel drop optik 1 core dengan messenger kawat baja untuk tarikan ke rumah pelanggan',
                'satuan_id' => $unitMeter->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'KBL-003',
                'nama' => 'Kabel LAN UTP Cat6 Solid Pure Copper 24AWG',
                'merek' => 'Belden',
                'deskripsi' => 'Kabel jaringan UTP Cat6 1000 Mbps untuk instalasi LAN indoor & rack server',
                'satuan_id' => $unitMeter->id,
                'vendor_id' => $vSolusi?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'KBL-004',
                'nama' => 'Kabel LAN FTP Cat5e Outdoor Shielded Double Jacket',
                'merek' => 'Spectra',
                'deskripsi' => 'Kabel jaringan LAN outdoor tahan cuaca dengan pelindung aluminium foil',
                'satuan_id' => $unitMeter->id,
                'vendor_id' => $vSolusi?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'KBL-005',
                'nama' => 'Patch Cord Fiber SC-UPC to SC-UPC Simplex 3M',
                'merek' => 'Corning',
                'deskripsi' => 'Kabel jumper serat optik SC-UPC 3 meter warna kuning',
                'satuan_id' => $unitPcs->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => false,
                'stok' => 120,
            ],
            [
                'kode_barang' => 'KBL-006',
                'nama' => 'Patch Cord Fiber SC-APC to SC-UPC Simplex 5M',
                'merek' => 'Corning',
                'deskripsi' => 'Kabel jumper serat optik konektor hijau ke biru 5 meter',
                'satuan_id' => $unitPcs->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => false,
                'stok' => 85,
            ],
            [
                'kode_barang' => 'KBL-007',
                'nama' => 'Patch Cord LAN Cat6 UTP 1.5M Blue Factory Moulded',
                'merek' => 'AMP Netconnect',
                'deskripsi' => 'Kabel patch cord RJ45 pabrikan 1.5 meter untuk koneksi switch ke server',
                'satuan_id' => $unitPcs->id,
                'vendor_id' => $vSolusi?->id,
                'has_serial_number' => false,
                'stok' => 150,
            ],
            [
                'kode_barang' => 'KBL-008',
                'nama' => 'Kabel Power C13 to Schuko Heavy Duty 1.8M',
                'merek' => 'Delta',
                'deskripsi' => 'Kabel daya listrik 3 pin standar EU untuk UPS, router, dan power supply',
                'satuan_id' => $unitPcs->id,
                'vendor_id' => $vSolusi?->id,
                'has_serial_number' => false,
                'stok' => 60,
            ],

            // ==========================================
            // D. CONNECTOR & MATERIAL (NON-SERIALIZED)
            // ==========================================
            [
                'kode_barang' => 'MAT-001',
                'nama' => 'Connector RJ45 Cat6 Gold Plated 50 Micron',
                'merek' => 'AMP / CommScope',
                'deskripsi' => 'Konektor RJ45 Cat6 dengan pin lapis emas untuk transmisi Gigabit',
                'satuan_id' => $unitPcs->id,
                'vendor_id' => $vSolusi?->id,
                'has_serial_number' => false,
                'stok' => 500,
            ],
            [
                'kode_barang' => 'MAT-002',
                'nama' => 'Fast Connector Fiber Optic SC/UPC Biru',
                'merek' => 'Huawei',
                'deskripsi' => 'Konektor cepat serat optik tanpa perlu proses fusion splicing di lapangan',
                'satuan_id' => $unitPcs->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => false,
                'stok' => 300,
            ],
            [
                'kode_barang' => 'MAT-003',
                'nama' => 'Fast Connector Fiber Optic SC/APC Hijau',
                'merek' => 'Huawei',
                'deskripsi' => 'Konektor cepat serat optik SC/APC low loss untuk terminasi ODP/ONT',
                'satuan_id' => $unitPcs->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => false,
                'stok' => 250,
            ],
            [
                'kode_barang' => 'MAT-004',
                'nama' => 'Protection Sleeve Fiber Optic 60mm Pelindung Core',
                'merek' => 'OpticSafe',
                'deskripsi' => 'Selongsong pelindung hasil sambungan serat optik dengan batang kawat baja',
                'satuan_id' => $unitPcs->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => false,
                'stok' => 800,
            ],
            [
                'kode_barang' => 'MAT-005',
                'nama' => 'Adapter Fiber SC-UPC to SC-UPC Simplex Biru',
                'merek' => 'Ilsintech',
                'deskripsi' => 'Kopler adapter SC-UPC untuk pemasangan pada ODF dan OTB',
                'satuan_id' => $unitPcs->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => false,
                'stok' => 180,
            ],
            [
                'kode_barang' => 'MAT-006',
                'nama' => 'Cable Tie Nylon 200mm x 3.6mm Hitam UV Resistant',
                'merek' => 'KSS',
                'deskripsi' => 'Tali pengikat kabel tahan cuaca luar ruangan isi 100 pcs per pack',
                'satuan_id' => $unitPack->id,
                'vendor_id' => $vSolusi?->id,
                'has_serial_number' => false,
                'stok' => 45,
            ],
            [
                'kode_barang' => 'MAT-007',
                'nama' => 'Insulation Tape PVC Electrical 3/4 Inch Hitam',
                'merek' => 'Unibell',
                'deskripsi' => 'Isolasi listrik hitam tahan tegangan tinggi untuk sambungan kabel',
                'satuan_id' => $unitRoll->id,
                'vendor_id' => $vSolusi?->id,
                'has_serial_number' => false,
                'stok' => 70,
            ],
            [
                'kode_barang' => 'MAT-008',
                'nama' => 'Optical Termination Box (OTB) 8 Core Wallmount',
                'merek' => 'PAZ',
                'deskripsi' => 'Kotak terminasi optik dinding 8 port untuk distribusi internal gedung',
                'satuan_id' => $unitPcs->id,
                'vendor_id' => $vFiber?->id,
                'has_serial_number' => false,
                'stok' => 35,
            ],

            // ==========================================
            // E. PERALATAN TEKNISI (TOOLS)
            // ==========================================
            [
                'kode_barang' => 'TOL-001',
                'nama' => 'Tang Crimping RJ45/RJ11 Modular CP-376VR',
                'merek' => "Pro'sKit",
                'deskripsi' => 'Tang presisi untuk pemasangan konektor RJ45 Cat5/Cat6 dan RJ11',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vMitra?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'TOL-002',
                'nama' => 'Toolkit Lengkap Teknisi ISP Fiber & Network PK-4028',
                'merek' => "Pro'sKit",
                'deskripsi' => 'Tas toolkit lengkap berisi obeng, tang, VFL, stripper, dan cleaver',
                'satuan_id' => $unitSet->id,
                'vendor_id' => $vMitra?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'TOL-003',
                'nama' => 'Digital Multimeter Auto-Ranging CD800a',
                'merek' => 'Sanwa',
                'deskripsi' => 'Multitester digital presisi untuk cek tegangan AC/DC, arus, dan hambatan',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vMitra?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'TOL-004',
                'nama' => 'Cordless Impact Drill / Bor Tangan Baterai 12V GSB 120-LI',
                'merek' => 'Bosch',
                'deskripsi' => 'Mesin bor baterai tanpa kabel untuk pemasangan klem dinding dan pipa kabel',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vMitra?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'TOL-005',
                'nama' => 'Tang Potong & Kupas Kabel Heavy Duty 8 Inch',
                'merek' => 'Knipex',
                'deskripsi' => 'Tang potong kawat messenger baja dan pengupas kabel serbaguna',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vMitra?->id,
                'has_serial_number' => false,
                'stok' => 18,
            ],
            [
                'kode_barang' => 'TOL-006',
                'nama' => 'Obeng Set Precision Magnetic 24 in 1',
                'merek' => 'Xiaomi Wiha',
                'deskripsi' => 'Set mata obeng presisi bahan baja S2 untuk buka perangkat elektronik dan router',
                'satuan_id' => $unitSet->id,
                'vendor_id' => $vSolusi?->id,
                'has_serial_number' => false,
                'stok' => 12,
            ],

            // ==========================================
            // F. PERLENGKAPAN KANTOR & SUPPORT
            // ==========================================
            [
                'kode_barang' => 'OFF-001',
                'nama' => 'Laptop Teknisi Lapangan ThinkPad T14 Gen 3 Core i5 16GB',
                'merek' => 'Lenovo',
                'deskripsi' => 'Laptop teknisi konfigurasi router, staging perangkat, dan monitoring jaringan',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vNetwork?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'OFF-002',
                'nama' => 'Monitor LED 24 Inch Full HD IPS 24MK600M',
                'merek' => 'LG',
                'deskripsi' => 'Monitor display dashboard monitoring NOC dan pengujian CCTV',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vNetwork?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'OFF-003',
                'nama' => 'UPS Online Smart-UPS 1000VA / 900W 230V LCD',
                'merek' => 'APC by Schneider',
                'deskripsi' => 'Catu daya cadangan baterai tanpa jeda untuk perangkat server & OLT',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vNetwork?->id,
                'has_serial_number' => true,
                'stok' => 0,
            ],
            [
                'kode_barang' => 'OFF-004',
                'nama' => 'Kursi Kerja Kantor Ergonomis Mesh Backrest',
                'merek' => 'Chitose',
                'deskripsi' => 'Kursi kerja staf teknisi dan meja administrasi logistik',
                'satuan_id' => $unitPcs->id,
                'vendor_id' => $vSolusi?->id,
                'has_serial_number' => false,
                'stok' => 45,
            ],
            [
                'kode_barang' => 'OFF-005',
                'nama' => 'Meja Kerja Teknisi & Staging Area Heavy Metal',
                'merek' => 'Informa',
                'deskripsi' => 'Meja staging perakitan server dan pengujian perangkat sebelum pasang',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vSolusi?->id,
                'has_serial_number' => false,
                'stok' => 8,
            ],
            [
                'kode_barang' => 'OFF-006',
                'nama' => 'Rak Server 19 Inch 15U Wallmount Glass Door',
                'merek' => 'Indorack',
                'deskripsi' => 'Rak server kabinet dinding untuk penempatan OLT, Switch, dan Patch Panel',
                'satuan_id' => $unitUnit->id,
                'vendor_id' => $vNetwork?->id,
                'has_serial_number' => false,
                'stok' => 6,
            ],
        ];

        foreach ($barangs as $item) {
            $item['id_user'] = $adminId;
            $item['is_active'] = true;
            Barangs::firstOrCreate(
                ['kode_barang' => $item['kode_barang']],
                $item
            );
        }
    }
}
