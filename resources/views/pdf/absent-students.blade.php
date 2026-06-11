<!DOCTYPE html>
<html>
<head>
    <title>Laporan Siswa Belum Hadir</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body onload="window.print()">
    <h2>Laporan Siswa Belum Hadir</h2>
    <p>Tanggal: {{ $today->format('d F Y') }}</p>
    <table>
        <thead>
            <tr><th>No</th><th>Nama Siswa</th><th>Kelas</th></tr>
        </thead>
        <tbody>
            @foreach($absentStudents as $index => $s)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $s->name }}</td>
                <td>{{ $s->class->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>