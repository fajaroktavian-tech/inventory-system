<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class AttendanceClassExport implements FromCollection
{
    protected $classId, $date;

    public function __construct($classId, $date) {
        $this->classId = $classId;
        $this->date = $date;
    }

    public function collection()
    {
        return User::where('class_id', $this->classId)
            ->with(['attendances' => fn($q) => $q->whereDate('date', $this->date)])
            ->get()
            ->map(fn($student) => [
                'NIS' => $student->nis,
                'Nama' => $student->name,
                'Status' => $student->attendances->first()?->status ?? 'ALPA',
                'Catatan' => $student->attendances->first()?->note ?? '-'
            ]);
    }
}
