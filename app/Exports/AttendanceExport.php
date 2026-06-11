<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AttendanceExport implements FromQuery, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $startDate, $endDate, $classId;

    public function __construct($startDate, $endDate, $classId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->classId = $classId;
    }

    public function query()
    {
        $query = User::query()
            ->where('role', 'siswa')
            ->with(['class'])
            ->withCount([
                'attendances as total_hadir' => fn($q) => $q->whereBetween('date', [$this->startDate, $this->endDate])->where('status', 'hadir'),
                'attendances as total_terlambat' => fn($q) => $q->whereBetween('date', [$this->startDate, $this->endDate])->where('status', 'terlambat'),
                'attendances as total_izin' => fn($q) => $q->whereBetween('date', [$this->startDate, $this->endDate])->whereIn('status', ['izin', 'sakit', 'dispen'])
            ]);

        if ($this->classId) {
            $query->where('class_name_search', $this->classId);
        }

        return $query;
    }

    public function headings(): array
    {
        $range = Carbon::parse($this->startDate)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDate)->format('d/m/Y');
        return [
            ['LAPORAN REKAPITULASI ABSENSI SISWA'],
            ['Periode: ' . $range],
            [''], // Baris kosong
            ['Nama Siswa', 'NIS', 'Kelas', 'Hadir', 'Terlambat', 'Izin/Sakit', 'Persentase']
        ];
    }

    public function map($user): array
    {
        // Hitung hari kerja (Senin-Jumat)
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $hariEfektif = $start->diffInDaysFiltered(fn(Carbon $date) => $date->isWeekday(), $end) + ($start->isWeekday() ? 1 : 0);

        $totalMasuk = $user->total_hadir + $user->total_terlambat + $user->total_izin;
        $persentase = $hariEfektif > 0 ? round(($totalMasuk / $hariEfektif) * 100) : 0;

        return [
            $user->name,
            $user->nis,
            $user->class->name ?? '-',
            $user->total_hadir,
            $user->total_terlambat,
            $user->total_izin,
            $persentase . '%'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]],
        ];
    }
}