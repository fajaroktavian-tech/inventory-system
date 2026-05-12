<!DOCTYPE html>
<html>
<head>
    <title>Daftar Inventaris Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin-bottom: 5px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f9f9f9; font-weight: bold; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; font-style: italic; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daftar Inventaris Barang</h2>
        <p>SMKN 7 BALEENDAH</p>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th width="60">Stok</th>
                <th width="60">Satuan</th>
                <th width="70">Min. Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name ?? 'Tanpa Kategori' }}</td>
                <td class="text-center">{{ $item->stock }}</td>
                <td class="text-center">{{ $item->unit }}</td>
                <td class="text-center">{{ $item->min_stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Inventaris pada {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>