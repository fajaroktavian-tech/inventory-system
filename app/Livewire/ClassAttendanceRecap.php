<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SchoolCalendar;
use Barryvdh\DomPDF\Facade\Pdf;

class ClassAttendanceRecap extends Component
{
    public $startDate, $endDate, $search='';
    public $stats = [];
    public $isDetailModalOpen = false;
public $selectedStudent = null;

    public function mount() {
        if (Auth::user()->role !== 'walikelas') abort(403);
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }
    public function render()
    {
        $classId = Auth::user()->class_id;

        // 1. Hitung total hari sekolah efektif dalam rentang tanggal
        $totalHariSekolah = SchoolCalendar::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('is_holiday', false)
            ->count();

        // 2. Ambil siswa dan absensinya
        $students = User::where('class_id', $classId)
            ->where('role', 'siswa')
            ->where('name', 'like', '%' . $this->search . '%')
            ->with(['attendances' => function($query) {
                $query->whereBetween('date', [$this->startDate, $this->endDate]);
            }])
            ->get();

        // 3. Kalkulasi per siswa
        foreach ($students as $student) {
            $hadir = $student->attendances->whereIn('status', ['hadir', 'terlambat'])->count();
            $lainnya = $student->attendances->whereIn('status', ['izin', 'sakit', 'dispen'])->count();
            
            // Logika Alpa: Total Hari Sekolah - Hari Hadir - Hari Izin/Sakit
            // Kita gunakan max(0, ...) agar tidak muncul angka negatif
            $student->alpa_count = max(0, $totalHariSekolah - ($hadir + $lainnya));
            $student->hadir_count = $hadir;
            $student->izin_sakit_count = $lainnya;
        }

        // Kalkulasi Statistik Keseluruhan
        $totalSiswa = $students->count();
        $totalHadir = $students->sum('hadir_count');
        $totalData = $totalSiswa * ($totalHariSekolah ?: 1);
        $persentase = $totalData > 0 ? ($totalHadir / $totalData) * 100 : 0;

        return view('livewire.class-attendance-recap', [
            'students' => $students,
            'totalSiswa' => $totalSiswa,
            'persentase' => round($persentase, 1)
        ]);
    }

    public function exportExcel()
    {
        // Mengirim data kelas ke class export
        return Excel::download(new \App\Exports\AttendanceExport(
            Auth::user()->class_id, $this->startDate, $this->endDate
        ), 'Rekap_Absensi_Kelas.xlsx');
    }
    public function showDetail($studentId)
{
    $this->selectedStudent = \App\Models\User::with(['attendances' => function($query) {
        $query->whereBetween('date', [$this->startDate, $this->endDate])
              ->orderBy('date', 'desc');
    }])->find($studentId);

    $this->isDetailModalOpen = true;
}
public function exportDetailPdf($studentId)
{
    $student = \App\Models\User::with(['attendances' => function($query) {
        $query->whereBetween('date', [$this->startDate, $this->endDate])
              ->orderBy('date', 'desc');
    }])->findOrFail($studentId);

    $pdf = Pdf::loadView('pdf.student-attendance-detail', [
        'student' => $student,
        'startDate' => $this->startDate,
        'endDate' => $this->endDate
    ]);

    return response()->streamDownload(
        fn() => print($pdf->output()), 
        'Absensi_' . $student->name . '_' . $this->startDate . '_sd_' . $this->endDate . '.pdf'
    );
}
}
