<?php

namespace App\Exports;

use App\Models\Attendance;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceRealExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Attendance::with(['student.class'])
            ->where('date', Carbon::today())
            ->get();
    }

    public function headings(): array
    {
        return ['Nama Siswa', 'Kelas', 'Waktu Masuk', 'Waktu Pulang', 'Status'];
    }

    public function map($attendance): array
    {
        return [
            $attendance->student->name,
            $attendance->student->class->name ?? '-',
            $attendance->time_in,
            $attendance->time_out ?? '-',
            strtoupper($attendance->status),
        ];
    }
}