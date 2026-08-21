<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;

class VendorExport implements FromCollection, WithHeadings, WithStyles
{
    protected $vendors;

    public function __construct(Collection $vendors)
    {
        $this->vendors = $vendors;
    }

    public function collection()
    {
        return $this->vendors->map(function ($item, $index) {
            return [
                'no' => $index + 1,
                'code' => $item->code ?? '-',
                'name' => $item->name,
                'phone' => $item->phone ?? '-',
                'email' => $item->email ?? '-',
                'address' => $item->address ?? '-',
                'description' => $item->description ?? '-',
                'barangs_count' => (string) ($item->barangs_count ?? $item->barangs()->count()),
                'created_at' => $item->created_at ? Carbon::parse($item->created_at)->translatedFormat('d-m-Y H:i') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Vendor',
            'Nama Vendor',
            'Telepon',
            'Email',
            'Alamat',
            'Deskripsi',
            'Jumlah Barang',
            'Tanggal Dibuat',
        ];
    }

    public function styles($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(12)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:I1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:I1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('696CFF');
        
        if ($highestRow > 1) {
            $sheet->getStyle('A2:I' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A2:I' . $highestRow)->getFont()->setSize(10);
        }
        
        $sheet->getStyle('A1:I' . $highestRow)->getAlignment()->setWrapText(true);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
