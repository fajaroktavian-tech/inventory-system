<?php

namespace App\Livewire\Absensi;

use Livewire\Component;
use App\Models\Attendance;
use Flux\Flux;
use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;

class PiketEntry extends Component
{
    public $search = ''; // Harus didefinisikan sebagai public
    public $isPiketModalOpen = false;
    public $selectedStudentId = null;
    public $isPiketGuideOpen = false;

    public function render()
    {
        $foundStudents = collect(); // Inisialisasi collection kosong

        if (!empty($this->search)) {
            $foundStudents = User::where('role', 'siswa')
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nis', 'like', '%' . $this->search . '%');
                })
                ->with('class')
                ->limit(10)
                ->get();
        }

        $latestAttendances = Attendance::where('verified_by', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->with('student') // Pastikan ada relasi student di model Attendance
            ->latest('updated_at')
            ->take(10)
            ->get();

        $attendedIds = Attendance::whereDate('date', now()->toDateString())->pluck('user_id');
        $absentStudents = User::where('role', 'siswa')
            ->where('is_active', true)
            ->whereNotIn('id', $attendedIds)
            ->with('class')
            ->limit(20) // Batasi agar tidak terlalu panjang
            ->get();

        return view('livewire.piket-entry', [
            'foundStudents' => $foundStudents,
            'latestAttendances' => $latestAttendances,
            'absentStudents' => $absentStudents
        ]);
    }

    public function markAsPresent($studentId, $status)
    {
        $now = Carbon::now();

        // 1. Ambil jam masuk (start_time) untuk hari ini
        // Asumsi: Anda punya jadwal yang aktif
        $schedule = Schedule::where('is_active', true)->first();

        $finalStatus = $status; // Default 'hadir'

        if ($schedule) {
            $startTime = Carbon::parse($schedule->start_time);

            // 2. Jika waktu sekarang lebih besar dari jam masuk, ubah status jadi terlambat
            if ($now->greaterThan($startTime)) {
                $finalStatus = 'terlambat';
            }
        }

        // 3. Simpan dengan status yang sudah divalidasi
        Attendance::updateOrCreate(
            ['user_id' => $studentId, 'date' => $now->toDateString()],
            [
                'status' => $finalStatus,
                'time_in' => $now->format('H:i:s'),
                'verified_by' => auth()->id()
            ]
        );

        Flux::toast('Data berhasil disimpan sebagai ' . $finalStatus);
        $this->reset('search');
    }

    public function openPiketModal($studentId)
    {
        $this->selectedStudentId = $studentId;
        $this->isPiketModalOpen = true;
    }

    public function savePiketStatus($status, $note = null)
    {
        Attendance::updateOrCreate(
            ['user_id' => $this->selectedStudentId, 'date' => now()->toDateString()],
            [
                'status' => $status,
                'note' => $note,
                'verified_by' => auth()->id()
            ]
        );

        $this->isPiketModalOpen = false;
        $this->reset(['search', 'selectedStudentId']);
        Flux::toast('Status absensi berhasil diperbarui.');
    }

    public function undoAttendance($attendanceId)
    {
        $attendance = Attendance::find($attendanceId);
        if ($attendance) {
            $attendance->delete();
            Flux::toast('Input berhasil dihapus (Undo).');
        }
    }
}
