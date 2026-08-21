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
            $barang = $item->barang;

            return [
                'No' => $index + 1,
                'Kode Barang' => $item->kode_barang,
                'Nama Barang' => $barang ? $barang->nama . ' - ' . $barang->merek : 'Tidak Ada Barang',
                'Kode Barang (Barang)' => $barang ? $barang->kode_barang : 'Tidak Ada Barang',
                'Jumlah' => $item->jumlah,
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
            'Tanggal Kembali',
            'Nama Peminjam',
            'Status',
        ];
    }

    public function styles($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:H1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2196F3');
        $sheet->getStyle('A2:H' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A1:H' . $highestRow)->getAlignment()->setWrapText(true);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
