<?php

namespace App\Livewire\Absensi;

use App\Models\ClassModel;
use App\Models\Attendance;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceDetailClassExport;
use Illuminate\Support\Str;

class AttendanceDetailByClass extends Component
{
    public $classId;
    public $note;
    public $date;
    public $class;
    public $studentsWithAttendance = [];
    public $isEditModalOpen = false;
    public $selectedStudent;
    public $selectedStudentName;
    public $currentStatus;
    public $selectedStudentId;
    public $search = '';
    
    public function updatedSearch()
    {
        $this->loadData();
    }
    public function mount($classId, $date)
    {
        $this->classId = $classId;
        $this->date = $date;
        $this->loadData(); 
    }
    public function loadData()
    {
        $this->class = ClassModel::with(['waliKelas', 'prodi'])->findOrFail($this->classId);

        $query = $this->class->students()->orderBy('name');

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
            // Opsional: Tambahkan pencarian berdasarkan NIS juga
            // ->orWhere('nis', 'like', '%' . $this->search . '%');
        }

        $students = $query->get();

        $attendances = Attendance::whereDate('date', $this->date)
            ->whereIn('user_id', $students->pluck('id'))
            ->get()
            ->keyBy('user_id');

        $this->studentsWithAttendance = $students->map(function ($student) use ($attendances) {
            $att = $attendances->get($student->id);

            return [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'attendance_record_id' => $att ? $att->id : null,
                'attendance_status' => $att ? $att->status : null,
                'time_in' => $att ? $att->time_in : null,
                'time_out' => $att ? $att->time_out : null,
                'note' => $att ? $att->note : null, 
            ];
        });
    }
    public function openEditModal($studentId)
    {
        $student = User::findOrFail($studentId);
        $this->selectedStudent = $student;
        $this->selectedStudentName = $student->name;

        // Cari record absensi yang ada
        $attendance = Attendance::where('user_id', $studentId)
            ->whereDate('date', $this->date)
            ->first();

        // Set status saat ini. Jika belum ada, default ke 'alpa' (atau 'hadir' silakan sesuaikan)
        $this->currentStatus = $attendance ? $attendance->status : 'alpa';

        $this->isEditModalOpen = true;
    }

    public function updateAttendance()
    {
        // Validasi sederhana
        $this->validate([
            'currentStatus' => 'required|in:hadir,terlambat,sakit,izin,dispen,alpa',
            'note'          => 'nullable|string|max:255',
        ]);

        $validatedBy = Auth::id();

        Attendance::updateOrCreate(
            [
                'user_id' => $this->selectedStudent->id,
                'date' => $this->date,
            ],
            [
                'status' => $this->currentStatus,
                'time_in' => in_array($this->currentStatus, ['hadir', 'terlambat'])
                    ? null 
                    : null,
                'time_out' => null, 
                'validated_by' => $validatedBy,
                'note' => $this->note,
            ]
        );

        $this->isEditModalOpen = false;
        $this->reset(['selectedStudent', 'currentStatus','note']); // Reset form modal
        $this->loadData(); 

        // Tampilkan notifikasi sukses (opsional, pakai flux toast kalau sudah ada)
        session()->flash('message', 'Status absensi berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.absensi.attendance-detail-by-class')->layout('layouts.app');
    }
    public function export()
    {
        $formattedDate = Carbon::parse($this->date)->format('Y-m-d');
        $fileName = 'Detail-Absen-' . Str::slug($this->class->name) . '-' . $formattedDate . '.xlsx';

        return Excel::download(new AttendanceDetailClassExport(
            collect($this->studentsWithAttendance), 
            $this->class->name,
            $this->date
        ), $fileName);
    }
}