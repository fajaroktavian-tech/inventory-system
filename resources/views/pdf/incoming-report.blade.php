<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN BARANG MASUK</h2>
        <p>SMKN 7 BALEENDAH</p>
        @if($startDate && $endDate)
            <p>Periode: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Petugas</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomings as $incoming)
            <tr>
                <td>{{ date('d/m/Y', strtotime($incoming->date)) }}</td>
                <td>{{ $incoming->item->name }}</td>
                <td>{{ $incoming->quantity }} {{ $incoming->item->unit }}</td>
                <td>{{ $incoming->user->name }}</td>
                <td>{{ $incoming->description ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>