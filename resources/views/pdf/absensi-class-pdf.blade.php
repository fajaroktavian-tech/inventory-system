<style>
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 8px; text-align: left; }
</style>
<h2>Absensi Kelas: {{ $class->name }}</h2>
<p>Tanggal: {{ $date }}</p>
<table>
    <thead>
        <tr><th>Nama</th><th>Status</th><th>Catatan</th></tr>
    </thead>
    <tbody>
        @foreach($students as $s)
        <tr>
            <td>{{ $s->name }}</td>
            <td>{{ $s->attendances->first()?->status ?? 'ALPA' }}</td>
            <td>{{ $s->attendances->first()?->note ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>