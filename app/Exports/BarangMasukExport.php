<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BarangMasukExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
{
    protected $barangMasuk;

    public function __construct($barangMasuk)
    {
        $this->barangMasuk = $barangMasuk;
    }

    public function collection()
    {
        return $this->barangMasuk->map(function ($item, $index) {
            return [
                'No' => $index + 1,
                'Kode Transaksi' => $item->kode_barang,
                'Nama Barang' => optional($item->barang)->nama,
                'Merek' => optional($item->barang)->merek,
                'Nomor Seri' => optional($item->inventoryItem)->serial_number ?? '-',
                'Jumlah' => (float) $item->jumlah,
                'Satuan' => optional(optional($item->barang)->unit)->symbol ?? 'pcs',
                'Ruangan' => optional($item->ruangan)->nama_ruangan ?? 'Gudang Utama',
                'Tanggal Masuk' => $item->tanggal_masuk?->format('d-m-Y') ?? '-',
                'Keterangan' => $item->keterangan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 'Kode Transaksi', 'Nama Barang', 'Merek', 'Nomor Seri', 'Jumlah', 'Satuan', 'Ruangan', 'Tanggal Masuk', 'Keterangan',
        ];
    }

    public function styles($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->setSize(11)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2196F3');
        $sheet->getStyle('A2:J' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A1:J' . $highestRow)->getAlignment()->setWrapText(true);

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
