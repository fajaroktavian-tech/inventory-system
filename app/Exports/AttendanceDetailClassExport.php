<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Illuminate\Support\Str;

class AttendanceDetailClassExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    protected $studentsData;
    protected $className;
    protected $date;

    public function __construct($studentsData, $className, $date)
    {
        $this->studentsData = $studentsData;
        $this->className = $className;
        $this->date = $date;
    }

    public function collection()
    {
        return $this->studentsData;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'NIS', 
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Catatan',
        ];
    }
    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row['name'],
            $row['nis'] ?? '-',
            $row['time_in'] ? Carbon::parse($row['time_in'])->format('H:i') : '',
            $row['time_out'] ? Carbon::parse($row['time_out'])->format('H:i') : '',
            strtoupper($row['attendance_status'] ?? 'ALPA'),
            $row['note'] ?? '',
        ];
    }
    public function title(): string
    {
        $formattedDate = Carbon::parse($this->date)->format('d-m-Y');
        return Str::limit('Detail-' . $this->className . '-' . $formattedDate, 31, '');
    }
    public function columnFormats(): array
    {
        return [
            'D' => 'h:mm',
            'E' => 'h:mm',
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }
}