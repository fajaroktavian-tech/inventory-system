<?php

namespace App\Livewire\Absensi;

use Livewire\Component;
use App\Models\Attendance;
use Flux\Flux;
use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;
use App\Models\SchoolCalendar;

class PiketEntry extends Component
{
    public $search = ''; // Harus didefinisikan sebagai public
    public $isPiketModalOpen = false;
    public $selectedStudentId = null;
    public $isPiketGuideOpen = false;

    public function render()
    {
        $today = now()->toDateString();

        // Cek apakah hari ini libur
        $isHoliday = SchoolCalendar::where('date', $today)
            ->where('is_holiday', true)
            ->exists();

        // Jika libur, kita kosongkan data absensi agar tidak membingungkan petugas
        $latestAttendances = $isHoliday ? collect() : Attendance::where('verified_by', auth()->id())
            ->whereDate('date', $today)
            ->with('student')
            ->latest('updated_at')
            ->take(10)
            ->get();

        $absentStudents = $isHoliday ? collect() : User::where('role', 'siswa')
            ->where('is_active', true)
            ->whereNotIn('id', Attendance::whereDate('date', $today)->pluck('user_id'))
            ->with('class')
            ->limit(20)
            ->get();

        return view('livewire.piket-entry', [
            'isHoliday' => $isHoliday, // Kirim status ke view
            'foundStudents' => ($isHoliday) ? collect() : $this->getFoundStudents(),
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

        if (SchoolCalendar::where('date', now()->toDateString())->where('is_holiday', true)->exists()) {
            Flux::toast('Tidak bisa input absen di hari libur!', 'danger');
            return;
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

        if (SchoolCalendar::where('date', now()->toDateString())->where('is_holiday', true)->exists()) {
            Flux::toast('Tidak bisa input absen di hari libur!', 'danger');
            return;
        }

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

    private function getFoundStudents()
    {
        if (empty($this->search))
            return collect();

        return User::where('role', 'siswa')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%');
            })
            ->with('class')
            ->limit(10)
            ->get();
    }
}
