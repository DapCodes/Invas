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
                'Kode Transaksi' => $item->kode_barang,
                'Nama Peminjam' => $item->nama_peminjam,
                'Nama Barang' => optional($item->barang)->nama,
                'Merek' => optional($item->barang)->merek,
                'Nomor Seri' => optional($item->inventoryItem)->serial_number ?? '-',
                'Jumlah' => (float) $item->jumlah,
                'Satuan' => optional(optional($item->barang)->unit)->symbol ?? 'pcs',
                'Tanggal Pinjam' => \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d-m-Y'),
                'Batas Kembali' => \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y'),
                'Status' => $item->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Transaksi',
            'Nama Peminjam',
            'Nama Barang',
            'Merek',
            'Nomor Seri',
            'Jumlah',
            'Satuan',
            'Tanggal Pinjam',
            'Batas Kembali',
            'Status',
        ];
    }

    public function styles($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:K1')->getFont()->setBold(true)->setSize(11)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF9800');
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
