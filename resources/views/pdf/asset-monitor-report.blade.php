<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Monitoring Aset</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        h2 { text-align: center; margin-bottom: 5px; }
        p.subtitle { text-align: center; margin-top: 0; color: #666; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        tr:nth-child(even) { background-color: #fafafa; }
    </style>
</head>
<body>
    <h2>Laporan Monitoring Inventaris Aset</h2>
    <p class="subtitle">Dicetak pada: {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Aset & SN</th>
                <th>Lokasi Ruangan</th>
                <th>PIC</th>
                <th>Peminjam</th>
                <th>Kondisi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $asset->itemInfo->name ?? '-' }}</strong><br>
                        <span style="font-family: monospace; color: #666;">SN: {{ $asset->serial_number ?? '-' }}</span>
                    </td>
                    <td>{{ $asset->room->name ?? '-' }}</td>
                    <td>{{ $asset->pic->name ?? '-' }}</td>
                    <td>{{ $asset->activeLoan->user->name ?? '-' }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $asset->condition)) }}</td>
                    <td>{{ strtoupper($asset->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>