<?php

namespace App\Livewire\Absensi;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use App\Models\ClassModel;

class DashboardAbsen extends Component
{
    public function render()
    {
        $today = Carbon::today();
        $totalSiswa = User::where('role', 'siswa')->where('is_active', true)->count();
        $totalHadir = Attendance::where('date', $today)->whereIn('status', ['hadir', 'terlambat'])->count();
        // 1. Ambil ID siswa yang sudah absen hari ini
        $attendedIds = Attendance::where('date', $today)
            ->pluck('user_id');

        // 2. Ambil semua siswa yang belum absen
        $absentStudents = User::where('role', 'siswa')
            ->where('is_active', true)
            ->whereNotIn('id', $attendedIds)
            ->with('class') // Mengambil relasi kelas agar bisa ditampilkan
            ->orderBy('class_id')
            ->get();

        $stats = [
            'total_hadir' => $totalHadir,
            'persentase' => $totalSiswa > 0 ? number_format(($totalHadir / $totalSiswa) * 100, 1) : 0,
            'terlambat' => Attendance::where('date', $today)->where('status', 'terlambat')->count(),
            'sakit_izin'   => Attendance::where('date', $today)->whereIn('status', ['sakit', 'izin', 'dispen'])->count(),
            'alpa'         => $absentStudents->count(), // Menggunakan hasil query di atas
        ];

        // 2. Logic Tren Kehadiran (7 Hari Terakhir)
        $dates = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i)->format('Y-m-d'));

        $trendData = Attendance::whereIn('date', $dates)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->selectRaw('date, count(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $lineChartData = $dates->map(fn($date) => $trendData->get($date, 0));
        $lineCategories = $dates->map(fn($date) => Carbon::parse($date)->translatedFormat('D, d M'));

        // 3. Logic Bar Chart (Per Kelas - Hari Ini)
        $classData = ClassModel::withCount([
            'users as students_count' => function ($query) use ($today) {
                // Pastikan kita memfilter user yang role-nya adalah 'siswa'
                $query->where('role', 'siswa')
                    ->whereHas('attendances', fn($q) => $q->where('date', $today)->whereIn('status', ['hadir', 'terlambat']));
            }
        ])->get();

        return view('livewire.dashboard-absen', [
            'stats' => $stats,
            'lineChartData' => $lineChartData->toJson(),
            'lineCategories' => $lineCategories->toJson(),
            'classNames' => $classData->pluck('name')->toJson(),
            'classCounts' => $classData->pluck('students_count')->toJson(),
            'absentStudents' => $absentStudents,
            'totalSiswa'     => $totalSiswa,

            // Data Tambahan untuk Tabel
            'recentTaps' => Attendance::with(['student.class'])
                ->where('date', $today)
                ->latest('updated_at')
                ->take(5)
                ->get(),

            'lateStudents' => Attendance::with(['student.class'])
                ->where('date', $today)
                ->where('status', 'terlambat')
                ->latest('time_in')
                ->get(),
        ]);
    }
}