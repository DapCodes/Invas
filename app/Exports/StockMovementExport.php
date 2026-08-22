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

class StockMovementExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
{
    protected $movements;

    public function __construct($movements)
    {
        $this->movements = $movements;
    }

    public function collection()
    {
        return $this->movements->map(function ($item, $index) {
            return [
                'No' => $index + 1,
                'Waktu' => $item->tanggal?->format('d-m-Y H:i') ?? '-',
                'Tipe Mutasi' => strtoupper($item->type),
                'Nama Barang' => optional($item->barang)->nama,
                'Merek' => optional($item->barang)->merek,
                'Serial Number' => optional($item->inventoryItem)->serial_number ?? '-',
                'Perubahan Qty' => (float) $item->quantity,
                'Saldo Sebelum' => (float) $item->quantity_before,
                'Saldo Sesudah' => (float) $item->quantity_after,
                'Satuan' => optional(optional($item->barang)->unit)->symbol ?? 'pcs',
                'Ruangan' => optional($item->ruangan)->nama_ruangan ?? 'Gudang Utama',
                'Petugas' => optional($item->user)->name ?? 'Sistem',
                'Referensi' => $item->reference_type ? ($item->reference_type . ' #' . $item->reference_id) : '-',
                'Keterangan' => $item->keterangan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu Transaksi',
            'Tipe Mutasi',
            'Nama Barang',
            'Merek',
            'Serial Number',
            'Perubahan Qty',
            'Saldo Sebelum',
            'Saldo Sesudah',
            'Satuan',
            'Lokasi / Ruangan',
            'Petugas',
            'Referensi',
            'Keterangan',
        ];
    }

    public function styles($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:N1')->getFont()->setBold(true)->setSize(11)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:N1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:N1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('3F51B5');
        $sheet->getStyle('A2:N' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A1:N' . $highestRow)->getAlignment()->setWrapText(true);

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
