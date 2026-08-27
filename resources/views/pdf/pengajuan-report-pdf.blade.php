<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Sarpras Sekolah</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2, .header p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; font-size: 10pt; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; float: right; text-align: center; font-size: 10pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h2>
        <h3>DINAS PENDIDIKAN - SMKN 7 BALEENDAH</h3>
        <p>LAPORAN REKAPITULASI {{ strtoupper($reportType) === 'PROCUREMENT' ? 'PENGADAAN BARANG' : 'PEMELIHARAAN ASET' }}</p>
        <p style="font-size: 9pt; color: #663;">Periode Tanggal: {{ $startDate }} s.d {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pemohon</th>
                @if($reportType === 'procurement')
                    <th>Nama Barang / Jenis</th>
                    <th>Qty</th>
                    <th>Estimasi Biaya</th>
                @else
                    <th>Aset / S/N / Ruangan</th>
                    <th>Kendala / Kerusakan</th>
                @endif
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->created_at->format('d/m/Y') }}</td>
                    <td>{{ $row->user->name ?? '-' }}</td>
                    
                    @if($reportType === 'procurement')
                        <td>{{ $row->item_name }} ({{ ucfirst($row->type) }})</td>
                        <td>{{ $row->qty }}</td>
                        <td>Rp {{ number_format($row->estimated_price * $row->qty, 0, ',', '.') }}</td>
                    @else
                        <td>
                            {{ $row->asset->itemInfo->name ?? '-' }} <br>
                            <small>S/N: {{ $row->asset->serial_number ?? '-' }} | Ruang: {{ $row->asset->room->name ?? '-' }}</small>
                        </td>
                        <td>{{ $row->damage_description }}</td>
                    @endif

                    <td>{{ ucwords(str_replace('_', ' ', $row->status)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Baleendah, {{ date('d F Y') }}</p>
        <p>Penanggung Jawab Sarpras,</p>
        <br><br><br>
        <p><b>( _________________________ )</b></p>
    </div>
</body>
</html>