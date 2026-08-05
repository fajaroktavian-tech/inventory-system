<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Peminjaman Aset</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h2 { text-align: center; margin-bottom: 5px; }
        p.subtitle { text-align: center; margin-top: 0; color: #666; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        tr:nth-child(even) { background-color: #fafafa; }
    </style>
</head>
<body onload="window.print()">
    <h2>Rekapitulasi Peminjaman Aset</h2>
    <p class="subtitle">Dicetak pada: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Aset & SN</th>
                <th>Tgl Pinjam</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $index => $loan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $loan->user->name ?? '-' }}</strong><br>
                        <small>{{ strtoupper($loan->user->role ?? '-') }}</small>
                    </td>
                    <td>
                        {{ $loan->asset->itemInfo->name ?? '-' }}<br>
                        <small style="color: #666;">SN: {{ $loan->asset->serial_number ?? '-' }}</small>
                    </td>
                    <td>{{ $loan->loan_date }}</td>
                    <td>{{ ucfirst($loan->status) }}</td>
                    <td>{{ $loan->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>