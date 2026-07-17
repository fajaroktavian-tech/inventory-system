<?php

namespace App\Livewire\Absensi;

use App\Models\Attendance;
use App\Models\User;
use App\Models\ClassModel;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Schedule;
use App\Models\SchoolCalendar;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceRealExport;

class AttendanceMonitor extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';

    // Render akan dipanggil otomatis setiap kali polling berjalan
    public function export()
{
    return Excel::download(new AttendanceRealExport, 'Absensi-' . Carbon::today()->format('Y-m-d') . '.xlsx');
}
    public function render()
    {
        $today = Carbon::today();
        $activeSchedule = Schedule::where('is_active', true)->first();

        // CEK APAKAH HARI INI LIBUR
        $isHoliday = SchoolCalendar::where('date', $today->format('Y-m-d'))
            ->where('is_holiday', true)
            ->exists();

        $limitTime = $activeSchedule ? $activeSchedule->start_time : '07:00:00';
        $totalSiswa = User::where('role', 'siswa')->count();

        // 1. Statistik Ringkasan
        $stats = [
            'hadir' => Attendance::where('date', $today)->whereIn('status', ['hadir', 'terlambat'])->count(),
            'terlambat' => Attendance::where('date', $today)
                ->where(function ($q) use ($limitTime) {
                    $q->where('status', 'terlambat')
                        ->orWhere('time_in', '>', $limitTime);
                })->count(),
            'izin_sakit' => Attendance::where('date', $today)->whereIn('status', ['izin', 'sakit', 'dispen'])->count(),

            // LOGIKA ALPA BARU: 
            // Jika libur, Alpa = 0. Jika sekolah, (Total - Absen).
            'tidak_hadir' => $isHoliday ? 0 : max(0, $totalSiswa - Attendance::where('date', $today)->count()),
        ];

        // 2. Daftar Aktivitas Terbaru (Log Absensi)
        $latestLogs = Attendance::with(['student.class'])
            ->where('date', $today)
            ->where(function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.attendance-monitor', [
            'stats' => $stats,
            'latestLogs' => $latestLogs
        ]);
    }
}