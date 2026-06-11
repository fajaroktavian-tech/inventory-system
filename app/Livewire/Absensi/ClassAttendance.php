<?php

namespace App\Livewire\Absensi;

use App\Models\User;
use App\Models\ClassModel;
use App\Models\Attendance;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Flux\Flux;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AttendanceClassExport; // Sesuaikan namespace Anda
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ClassAttendance extends Component
{
    public $selectedDate;
    public $class;
    public $isEditModalOpen = false;
    public $editingAttendanceId;
    public $newStatus;
    public $newNote;
    public $search = '';
    public $statusFilter = '';
    public $isGuideModalOpen = false;

    public function mount()
    {
        $this->selectedDate = now()->toDateString();
        // Cari kelas di mana user yang login adalah walikelasnya
        $this->class = ClassModel::whereHas('users', function ($query) {
            $query->where('id', Auth::id())->where('role', 'walikelas');
        })->first();
    }

    public function render()
    {
        $students = User::where('class_id', $this->class?->id ?? 0)
            ->where('role', 'siswa')
            ->where('name', 'like', '%' . $this->search . '%')
            ->when($this->statusFilter, function ($query) {
                // Kita gunakan whereHas untuk memastikan siswa tsb punya record dengan status tersebut
                $query->whereHas('attendances', function ($q) {
                    $q->whereDate('date', $this->selectedDate)
                        ->where('status', $this->statusFilter);
                });
            })
            ->with([
                'attendances' => function ($query) {
                    $query->whereDate('date', $this->selectedDate);
                }
            ])
            ->get();
        $baseQuery = User::where('class_id', $this->class?->id ?? 0)
            ->where('role', 'siswa');

        // 1. Total Siswa
        $totalSiswa = $baseQuery->count();

        // 2. Ambil data siswa dengan relasi absensi
        $students = $baseQuery->with([
            'attendances' => function ($query) {
                $query->whereDate('date', $this->selectedDate);
            }
        ])->get();

        // 3. Hitung Statistik
        $stats = [
            'hadir' => 0,
            'izin' => 0,
            'terlambat' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'dispen' => 0
        ];

        $isWeekend = Carbon::parse($this->selectedDate)->isWeekend();

        foreach ($students as $student) {
            $attendance = $student->attendances->first();

            if ($attendance) {
                $stats[$attendance->status]++;
            } else {
                // HANYA tambah ke Alpa jika BUKAN akhir pekan
                if (!$isWeekend) {
                    $stats['alpa']++;
                }
            }
        }

        // 4. Hitung Persentase (Kehadiran = Hadir + Terlambat + Dispen)
        $hadirTotal = $stats['hadir'] + $stats['dispen']; // Anda bisa sesuaikan definisi 'hadir'
        $persentase = $totalSiswa > 0 ? ($hadirTotal / $totalSiswa) * 100 : 0;

        return view('livewire.class-attendance', compact('students', 'totalSiswa', 'stats', 'persentase'));
    }

    public function openEditModal($attendanceId)
    {
        $attendance = Attendance::findOrFail($attendanceId);
        $this->editingAttendanceId = $attendance->id;
        $this->newStatus = $attendance->status;
        $this->newNote = $attendance->note;
        $this->isEditModalOpen = true;
    }

    public function saveStatus()
    {
        $this->validate([
            'newStatus' => 'required|in:hadir,terlambat,izin,sakit,alpa,dispen',
            'newNote' => 'nullable|string|max:255',
        ]);

        $attendance = Attendance::find($this->editingAttendanceId);

        // Update record
        $attendance->update([
            'status' => $this->newStatus,
            'note' => $this->newNote,
            'verified_by' => auth()->id(), // Mencatat siapa wali kelas yang mengedit
            'verified_at' => now(),
        ]);

        $this->isEditModalOpen = false;
        Flux::toast('Status absensi berhasil diperbarui.');
    }

    public function createAttendance($studentId)
    {
        // Logika untuk membuat record baru saat tombol + diklik
        Attendance::create([
            'user_id' => $studentId,
            'date' => $this->selectedDate,
            'status' => 'alpa', // Default awal
        ]);

        // Opsional: Langsung buka modal edit agar bisa langsung diubah statusnya
        $attendance = Attendance::where('user_id', $studentId)
            ->where('date', $this->selectedDate)
            ->first();

        $this->openEditModal($attendance->id);
    }

    // Tambahkan di ClassAttendance.php
    public function exportExcel()
    {
        // Tambahkan filter where('role', 'siswa')
        return Excel::download(new AttendanceClassExport($this->class->id, $this->selectedDate), 'Absensi_' . $this->class->name . '_' . $this->selectedDate . '.xlsx');
    }

    public function exportPdf()
    {
        $students = User::where('class_id', $this->class->id)
            ->where('role', 'siswa') // TAMBAHKAN INI
            ->with(['attendances' => fn($q) => $q->whereDate('date', $this->selectedDate)])
            ->get();

        $pdf = Pdf::loadView('pdf.absensi-class-pdf', [
            'students' => $students,
            'class' => $this->class,
            'date' => $this->selectedDate
        ]);

        return response()->streamDownload(fn() => print ($pdf->output()), 'Absensi_' . $this->selectedDate . '.pdf');
    }
}
