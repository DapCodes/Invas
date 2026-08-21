<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class BarangRuanganExport implements FromCollection, WithHeadings, WithStyles
{
    protected $barangRuangan;

    public function __construct($barangRuangan)
    {
        $this->barangRuangan = $barangRuangan;
    }

    public function collection()
    {
        return $this->barangRuangan->map(function ($item, $index) {
            return [
                'No' => $index + 1,
                'Nama Ruangan' => $item->ruangan ? $item->ruangan->nama_ruangan : '-',
                'Nama Barang' => $item->barang ? $item->barang->nama : '-',
                'Stok' => $item->stok,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Ruangan',
            'Nama Barang',
            'Stok',
        ];
    }

    public function styles($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2196F3');
        $sheet->getStyle('A2:D' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A1:D' . $highestRow)->getAlignment()->setWrapText(true);

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
