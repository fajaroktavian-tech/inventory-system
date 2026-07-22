<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>DIR - {{ $room->name }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 12px; }
        
        /* Layout Header pakai Tabel supaya DomPDF rapi */
        .header-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { width: 80px; }
        .school-info { text-align: center; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        
        /* Layout Footer pakai Tabel */
        .footer-table { width: 100%; margin-top: 50px; }
        .signature { text-align: center; width: 50%; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 100px; border: none;">
                <img src="{{ public_path('images/LogoSMKN7BE.png') }}" class="logo" alt="Logo">
            </td>
            <td class="school-info" style="border: none;">
                <h2 style="margin: 0;">SMKN 7 BALEENDAH</h2>
                <p style="margin: 0;">Jl. Siliwangi No.Km.15, Manggahang, Kec. Baleendah, Kabupaten Bandung, Jawa Barat 40375</p>
                <h3 style="margin: 10px 0 0 0;">DAFTAR INVENTARIS RUANGAN: {{ strtoupper($room->name) }}</h3>
            </td>
        </tr>
    </table>

    <!-- Tabel Aset -->
    <table>
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">No</th>
                <th>Nama Barang</th>
                <th>SN</th>
                <th>Kondisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($room->assets as $index => $asset)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $asset->itemInfo->name }}</td>
                    <td>{{ $asset->serial_number ?? '-' }}</td>
                    <td>{{ strtoupper(str_replace('_', ' ', $asset->condition)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="footer-table">
        <tr>
            <td class="signature" style="border: none;">
                <p>Penanggung Jawab Ruangan</p>
                <div class="signature-space"></div>
                <p><strong>{{ $room->assets->first()->pic->name ?? '..........................' }}</strong></p>
            </td>
            <td class="signature" style="border: none;">
                <p>Kepala Sarana Prasarana</p>
                <div class="signature-space"></div>
                <p><strong>( Gungun Gunawan, S.Pd )</strong></p>
            </td>
        </tr>
    </table>

</body>
</html>