<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Absensi Siswa</h2>
    <p>Nama: {{ $student->name }}</p>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Jam Masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($student->attendances as $att)
            <tr>
                <td>{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                <td>{{ strtoupper($att->status) }}</td>
                <td>{{ $att->time_in ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>