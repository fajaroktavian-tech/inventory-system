<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN INVENTARIS GUDANG</h2>
        <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    </div>

    @if($activeTab === 'stok')
        <h3>Laporan Stok Barang</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stok as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? '-' }}</td>
                        <td>{{ $item->stock }}</td>
                        <td>{{ $item->unit }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <h3>Laporan Barang Keluar</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Penerima</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($keluar as $log)
                    <tr>
                        <td>{{ date('d/m/Y', strtotime($log->request->request_date)) }}</td>
                        <td>{{ $log->request->student->name }} ({{ $log->request->class->name ?? '-' }})</td>
                        <td>{{ $log->item->name }}</td>
                        <td>{{ $log->quantity_approved }} {{ $log->item->unit }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dicetak pada: {{ $date }}
    </div>
</body>
</html>