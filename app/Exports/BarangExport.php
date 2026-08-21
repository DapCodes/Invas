<?php

namespace App\Exports;

use Illuminate\Support\Collection;
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

class BarangExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
{
    protected $barang; // Menampung data barang yang sudah difilter

    // Konstruktor untuk menerima data yang sudah difilter
    public function __construct(Collection $barang)
    {
        $this->barang = $barang;
    }

    // Menggunakan data yang diterima di konstruktor
    public function collection()
    {
        // Ambil hanya kolom yang relevan
        return $this->barang->map(function($item) {
            return [
                'kode_barang' => $item->kode_barang,
                'nama' => $item->nama,
                'merek' => $item->merek,
                'stok' => (string) $item->stok,  // pastikan stok diubah menjadi string
            ];
        });
    }

    public function headings(): array
    {
        return ['Kode Barang', 'Nama Barang', 'Merek', 'Stok'];
    }

    public function styles($sheet)
    {
        // Styling Header
        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12)
            ->setColor(new Color('FFFFFF')); // Set font color to white
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50'); // Background green color

        // Styling seluruh baris data
        $sheet->getStyle('A2:D' . $sheet->getHighestRow())
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // Border seluruh tabel

        // Menambahkan padding dan font yang lebih baik
        $sheet->getStyle('A1:D' . $sheet->getHighestRow())->getAlignment()->setWrapText(true); // Membungkus teks dalam sel

        // Menambahkan alignment dan ukuran font pada data
        $sheet->getStyle('A2:D' . $sheet->getHighestRow())
            ->getFont()->setSize(10);
        
        // Penyesuaian ukuran kolom agar pas
        $sheet->getColumnDimension('A')->setAutoSize(true); // Ukuran kolom Kode Barang
        $sheet->getColumnDimension('B')->setAutoSize(true); // Ukuran kolom Nama Barang
        $sheet->getColumnDimension('C')->setAutoSize(true); // Ukuran kolom Merek
        $sheet->getColumnDimension('D')->setAutoSize(true); // Ukuran kolom Stok

        return [
            'A' => ['width' => 15],
            'B' => ['width' => 30],
            'C' => ['width' => 20],
            'D' => ['width' => 15],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT,  // Menjadikan angka dalam kolom stok sebagai teks
        ];
    }
}
