<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PeminjamanExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
{
    protected $peminjamans;

    public function __construct($peminjamans)
    {
        $this->peminjamans = $peminjamans;
    }

    public function collection()
    {
        return $this->peminjamans->map(function ($item, $index) {
            return [
                'No' => $index + 1,
                'Kode Barang' => $item->kode_barang,
                'Nama Barang' => optional($item->barang)->nama . ' - ' . optional($item->barang)->merek,
                'Kode Barang (Barang)' => optional($item->barang)->kode_barang,
                'Jumlah' => $item->jumlah,
                'Tanggal Pinjam' => \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d-m-Y'),
                'Tanggal Kembali' => \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y'),
                'Nama Peminjam' => $item->nama_peminjam,
                'Status' => $item->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Kode Barang (Barang)',
            'Jumlah',
            'Tanggal Pinjam',
            'Tanggal Kembali',
            'Nama Peminjam',
            'Status',
        ];
    }

    public function styles($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(12)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:I1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50');
        $sheet->getStyle('A2:I' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A1:I' . $highestRow)->getAlignment()->setWrapText(true);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
