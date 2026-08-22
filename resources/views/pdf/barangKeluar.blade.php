<!DOCTYPE html>
<html>

<head>
    <title>Laporan Barang Keluar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header img {
            width: 110px;
        }

        .header h2 {
            flex-grow: 1;
            text-align: center;
            margin: 0;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 5px 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Data Barang Keluar</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Kode Transaksi</th>
                <th>Nama Barang & Merek</th>
                <th>Serial Number</th>
                <th>Jumlah Keluar</th>
                <th>Ruangan</th>
                <th>Tanggal Keluar</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barangKeluar as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $item->kode_barang }}</strong></td>
                    <td>{{ $item->barang?->nama }} ({{ $item->barang?->merek }})</td>
                    <td>{{ $item->inventoryItem?->serial_number ?? '-' }}</td>
                    <td>{{ number_format((float)$item->jumlah, $item->barang?->unit?->is_decimal ? 2 : 0) }} {{ $item->barang?->unit?->symbol ?? 'pcs' }}</td>
                    <td>{{ $item->ruangan?->nama_ruangan ?? 'Gudang Utama' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_keluar)->translatedFormat('d M Y') }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i') }} WIB
    </div>
</body>

</html>
