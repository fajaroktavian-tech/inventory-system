<?php

namespace App\Exports;

use App\Models\ClassModel;
use App\Models\Attendance;
use App\Models\SchoolCalendar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AttendanceRecapClassExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $selectedDate;
    protected $search;

    public function __construct($selectedDate, $search = '')
    {
        $this->selectedDate = $selectedDate;
        $this->search = $search;
    }

    public function collection()
    {
        $isHoliday = SchoolCalendar::where('date', $this->selectedDate)
            ->where('is_holiday', true)
            ->exists();

        $classes = ClassModel::with(['prodi', 'waliKelas', 'students'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->get();

        return $classes->map(function ($class) use ($isHoliday) {
            $students = $class->students;
            $totalStudents = $students->count();

            $hadir = 0; $terlambat = 0; $sakit = 0; $izin = 0; $alpa = 0; $dispen = 0;

            if (!$isHoliday && $totalStudents > 0) {
                $studentIds = $students->pluck('id');

                $attendances = Attendance::whereIn('user_id', $studentIds)
                    ->whereDate('date', $this->selectedDate)
                    ->get()
                    ->keyBy('user_id');

                foreach ($students as $student) {
                    $att = $attendances->get($student->id);

                    if ($att) {
                        switch ($att->status) {
                            case 'hadir': $hadir++; break;
                            case 'terlambat': $terlambat++; break;
                            case 'sakit': $sakit++; break;
                            case 'izin': $izin++; break;
                            case 'dispen': $dispen++; break;
                            case 'alpa': $alpa++; break;
                        }
                    } else {
                        $alpa++;
                    }
                }
            }

            $class->students_count = $totalStudents;
            $class->rekap = [
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'dispen' => $dispen,
            ];

            return $class;
        });
    }

    public function headings(): array
    {
        return [
            ['REKAPITULASI KEHADIRAN SISWA PER KELAS'],
            ['Tanggal: ' . Carbon::parse($this->selectedDate)->translatedFormat('d F Y')],
            [], // Baris kosong
            [
                'Nama Kelas',
                'Program Keahlian',
                'Wali Kelas',
                'Total Siswa',
                'Hadir',
                'Terlambat',
                'Sakit',
                'Izin',
                'Dispen',
                'Alpa'
            ]
        ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->prodi->name ?? '-',
            $row->waliKelas->name ?? 'Belum ditentukan',
            $row->students_count,
            $row->rekap['hadir'],
            $row->rekap['terlambat'],
            $row->rekap['sakit'],
            $row->rekap['izin'],
            $row->rekap['dispen'],
            $row->rekap['alpa'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge title rows
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');

        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']],
            2 => ['font' => ['italic' => true, 'size' => 10], 'alignment' => ['horizontal' => 'center']],
            4 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '4F46E5'] // Warna tema Indigo Tailwind
                ],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
            ],
        ];
    }
}