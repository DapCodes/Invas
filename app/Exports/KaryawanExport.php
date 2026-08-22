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

class KaryawanExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
{
    protected $karyawan;

    // Menambahkan parameter pada konstruktor untuk menerima data yang sudah difilter
    public function __construct($karyawan)
    {
        $this->karyawan = $karyawan;
    }

    // Menggunakan data yang sudah difilter
    public function collection()
    {
        return $this->karyawan->map(function ($user, $index) {
            return [
                'No' => $index + 1,
                'Nama' => $user->name,
                'Email' => $user->email,
                'Tanggal Daftar' => $user->created_at->format('d-m-Y'),  // Format tanggal
            ];
        });
    }

    // Judul kolom
    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Email',
            'Tanggal Daftar',
        ];
    }

    // Styling tabel Excel
    public function styles($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Styling header
        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2196F3');

        // Styling untuk data
        $sheet->getStyle('A2:D' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A2:D' . $highestRow)->getFont()->setSize(10);
        $sheet->getStyle('A1:D' . $highestRow)->getAlignment()->setWrapText(true);

        // Auto-size kolom
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }

    // Format kolom tertentu
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Format tanggal
        ];
    }
}
