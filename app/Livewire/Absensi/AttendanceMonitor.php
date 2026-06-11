<?php

namespace App\Livewire\Absensi;

use App\Models\Attendance;
use App\Models\User;
use App\Models\ClassModel;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Schedule;

class AttendanceMonitor extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';

    // Render akan dipanggil otomatis setiap kali polling berjalan
    public function render()
    {
        $today = Carbon::today();
        $activeSchedule = Schedule::where('is_active', true)->first();

        $limitTime = $activeSchedule ? $activeSchedule->start_time : '07:00:00';

        // 1. Statistik Ringkasan
        $stats = [
            'total_siswa' => User::where('role', 'siswa')->count(),
            'hadir' => Attendance::where('date', $today)->whereIn('status', ['hadir', 'terlambat'])->count(),
            'terlambat' => Attendance::where('date', $today)
                ->where(function($q) use ($limitTime) {
                    $q->where('status', 'terlambat')
                      ->orWhere('time_in', '>', $limitTime);
                })->count(),
            'tidak_hadir' => User::where('role', 'siswa')->count() - Attendance::where('date', $today)->count(),
            'izin_sakit' => Attendance::where('date', $today)->whereIn('status', ['izin', 'sakit', 'dispen'])->count(),
        ];

        // 2. Daftar Aktivitas Terbaru (Log Absensi)
        $latestLogs = Attendance::with(['student.class'])
            ->where('date', $today)
            ->where(function($query) {
                $query->whereHas('student', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function($q) {
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