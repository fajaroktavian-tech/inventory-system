<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Aset - {{ $asset->serial_number }}</title>
    <style>
        @page {
            size: 320pt 160pt; /* Ukuran pas untuk stiker label */
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 6px;
            background: #fff;
            color: #000;
        }
        .label-container {
            width: 308pt;
            height: 148pt;
            border: 1.5px solid #000;
            padding: 8px;
            box-sizing: border-box;
            background: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            vertical-align: middle;
        }
        .qr-section {
            width: 32%;
            text-align: center;
            border-right: 1px dashed #666;
            padding-right: 6px;
        }
        .qr-section img {
            width: 80px;
            height: 80px;
        }
        .sn-text {
            font-size: 6.5pt;
            font-weight: bold;
            margin-top: 3px;
            word-break: break-all;
        }
        .info-section {
            width: 68%;
            padding-left: 8px;
        }
        .school-name {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 3px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }
        .item-name {
            font-size: 8.5pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            line-height: 1.1;
        }
        .item-detail {
            font-size: 7pt;
            margin: 1.5px 0;
            color: #222;
        }
        .room-badge {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 4px;
            background-color: #eee;
            padding: 2px 4px;
            border: 1px solid #bbb;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="label-container">
        <table>
            <tr>
                <!-- Kolom Kiri: QR Code via Google Chart API (Aman & Langsung Jadi Gambar) -->
                <td class="qr-section">
                    @php
                        $qrData = $asset->serial_number ?? $asset->barcode_token;
                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrData);
                    @endphp
                    <img src="{{ $qrUrl }}" alt="QR Code">
                    <div class="sn-text">{{ $asset->serial_number }}</div>
                </td>

                <!-- Kolom Kanan: Informasi -->
                <td class="info-section">
                    <div class="school-name">SMKN 7 BALEENDAH</div>
                    <div class="item-name">{{ $asset->itemInfo->name }}</div>
                    <div class="item-detail"><b>Merk:</b> {{ $asset->itemInfo->brand ?? '-' }}</div>
                    <div class="item-detail"><b>S/N:</b> {{ $asset->serial_number ?? '-' }}</div>
                    <div class="room-badge">📍 {{ $asset->room->name }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>