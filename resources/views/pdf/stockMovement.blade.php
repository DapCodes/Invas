<!DOCTYPE html>
<html>
<head>
    <title>Buku Mutasi & Audit Trail Stok</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 15px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px 5px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 20px; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0; font-size: 16px;">Buku Mutasi Stok & Audit Trail</h2>
        <small>Sistem Informasi Manajemen Inventaris</small>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 20px;">#</th>
                <th>Waktu</th>
                <th>Tipe</th>
                <th>Barang & Merek</th>
                <th>Serial Number</th>
                <th>Perubahan</th>
                <th>Sebelum</th>
                <th>Sesudah</th>
                <th>Lokasi</th>
                <th>Petugas</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($movements as $m)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $m->tanggal?->translatedFormat('d/m/Y H:i') }}</td>
                    <td><strong>{{ strtoupper($m->type) }}</strong></td>
                    <td>{{ $m->barang?->nama }} ({{ $m->barang?->merek }})</td>
                    <td>{{ $m->inventoryItem?->serial_number ?? '-' }}</td>
                    <td><strong>{{ (float)$m->quantity > 0 ? '+' : '' }}{{ number_format((float)$m->quantity, 2) }}</strong></td>
                    <td>{{ number_format((float)$m->quantity_before, 2) }}</td>
                    <td>{{ number_format((float)$m->quantity_after, 2) }}</td>
                    <td>{{ $m->ruangan?->nama_ruangan ?? '-' }}</td>
                    <td>{{ $m->user?->name ?? 'Sistem' }}</td>
                    <td>{{ $m->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i') }} WIB
    </div>
</body>
</html>
