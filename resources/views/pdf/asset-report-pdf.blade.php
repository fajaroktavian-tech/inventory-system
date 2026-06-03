<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN REKAPITULASI POPULASI ASET</h2>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Merk</th>
                <th>Total</th>
                <th>Baik</th>
                <th>Rusak</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assetSummary as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->brand }}</td>
                <td>{{ $item->total_unit }}</td>
                <td>{{ $item->kondisi_baik }}</td>
                <td>{{ $item->kondisi_rusak }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>