<?php

namespace App\Livewire;

use App\Models\ClassModel;
use App\Models\Attendance;
use App\Models\SchoolCalendar;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Exports\AttendanceRecapClassExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceRecapByClass extends Component
{
    use WithPagination;
    public $search = '';
    public $selectedDate;

    public function mount()
    {
        // Default tanggal hari ini
        $this->selectedDate = Carbon::today()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedDate()
    {
        $this->resetPage();
    }
    public function render()
    {
        $isHoliday = SchoolCalendar::where('date', $this->selectedDate)
            ->where('is_holiday', true)
            ->exists();

        $classes = ClassModel::with(['prodi', 'waliKelas', 'students'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        $recapData = $classes->through(function ($class) use ($isHoliday) {
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

            $class->id = $class->id;
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

        return view('livewire.attendance-recap-by-class', [
            'classes' => $recapData
        ])->layout('layouts.app');
    }

    public function export()
    {
        $fileName = 'rekap-absensi-kelas-' . $this->selectedDate . '.xlsx';
        
        return Excel::download(new AttendanceRecapClassExport($this->selectedDate, $this->search), $fileName);
    }
}