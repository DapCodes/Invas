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

class PengembalianExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
{
    protected $pengembalians;

    public function __construct($pengembalians)
    {
        $this->pengembalians = $pengembalians;
    }

    public function collection()
    {
        return $this->pengembalians->map(function ($item, $index) {
            return [
                'No' => $index + 1,
                'Kode Pengembalian' => $item->kode_barang,
                'Nama Peminjam' => $item->nama_peminjam,
                'Nama Barang' => optional($item->barang)->nama,
                'Merek' => optional($item->barang)->merek,
                'Nomor Seri' => optional($item->inventoryItem)->serial_number ?? '-',
                'Jumlah Kembali' => (float) $item->jumlah,
                'Satuan' => optional(optional($item->barang)->unit)->symbol ?? 'pcs',
                'Kondisi' => $item->kondisi,
                'Tanggal Kembali' => \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y'),
                'Keterangan' => $item->keterangan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Pengembalian',
            'Nama Peminjam',
            'Nama Barang',
            'Merek',
            'Nomor Seri',
            'Jumlah Kembali',
            'Satuan',
            'Kondisi',
            'Tanggal Kembali',
            'Keterangan',
        ];
    }

    public function styles($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:K1')->getFont()->setBold(true)->setSize(11)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50');
        $sheet->getStyle('A2:K' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A1:K' . $highestRow)->getAlignment()->setWrapText(true);

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
