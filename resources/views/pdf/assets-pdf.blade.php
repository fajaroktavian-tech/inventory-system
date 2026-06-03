<!DOCTYPE html>
<html>
<head>
    <title>Laporan Inventaris Aset</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; }
        .info { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { mt-30px; text-align: right; margin-top: 30px; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 10px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA UNIT ASET</h2>
        <h3>SMKN 7 BALEENDAH</h3>
        <p>Tanggal Cetak: {{ now()->format('d F Y') }}</p>
    </div>

    <div class="info">
        @if($roomName) <strong>Ruangan:</strong> {{ $roomName }} <br> @endif
        @if($condition) <strong>Kondisi:</strong> {{ strtoupper(str_replace('_', ' ', $condition)) }} <br> @endif
        <strong>Total Unit:</strong> {{ $assets->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang / SN</th>
                <th>Lokasi</th>
                <th>PIC</th>
                <th>Kondisi</th>
                <th>Tahun</th>
                <th>Sumber Dana</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $asset->itemInfo->name }}</strong><br>
                    <span style="font-size: 10px; color: #555;">SN: {{ $asset->serial_number }}</span>
                </td>
                <td>{{ $asset->room->name }}</td>
                <td>{{ $asset->pic->name }}</td>
                <td>{{ strtoupper(str_replace('_', ' ', $asset->condition)) }}</td>
                <td>{{ $asset->acquisition_year }}</td>
                <td>{{ $asset->source_fund }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Baleendah, {{ now()->format('d F Y') }}</p>
        <br><br><br>
        <p><strong>( __________________________ )</strong><br>Petugas Inventaris</p>
    </div>
</body>
</html>