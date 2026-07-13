<?php

namespace App\Livewire\Absensi;

use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReport extends Component
{
    use WithPagination;

    // Properti filter baru
    public $startDate;
    public $isRecapGuideOpen = false;
    public $selectedStudentDetail = null;
    public $isDetailModalOpen = false;
    public $endDate;
    public $class_name_search = '';
    public $search = '';

    public function mount()
    {
        // Default: menampilkan rekap bulan berjalan (dari tanggal 1 sampai hari ini)
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->toDateString();
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        $classes = ClassModel::orderBy('name')->get();
        $hariEfektif = $this->getAktifDaysCount($this->startDate, $this->endDate);

        // 1. Inisiasi Query Utama
        $baseQuery = User::query()
            ->where('role', 'siswa')
            ->with(['class'])
            ->withCount([
                'attendances as total_hadir' => function ($q) {
                    $q->whereBetween('date', [$this->startDate, $this->endDate])
                        ->where('status', 'hadir');
                },
                'attendances as total_terlambat' => function ($q) {
                    $q->whereBetween('date', [$this->startDate, $this->endDate])
                        ->where('status', 'terlambat');
                },
                'attendances as total_izin' => function ($q) {
                    $q->whereBetween('date', [$this->startDate, $this->endDate])
                        ->whereIn('status', ['izin', 'sakit', 'dispen']);
                }
            ]);

        // 2. Terapkan Filter Kelas (Berdasarkan Nama karena menggunakan datalist)
        if ($this->class_name_search) {
            $baseQuery->whereHas('class', function ($q) {
                $q->where('name', $this->class_name_search);
            });
        }

        // 3. Terapkan Filter Nama/NIS
        if ($this->search) {
            $baseQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        // 4. Ambil data untuk Statistik (Tanpa Pagination)
        $allStudentsData = (clone $baseQuery)->get();

        // --- LOGIKA STATISTIK ---
        $totalHadirTepatWaktu = $allStudentsData->sum('total_hadir');
        $totalKapasitas = $allStudentsData->count() * $hariEfektif;
        $rataRataHadir = $totalKapasitas > 0 ? round(($totalHadirTepatWaktu / $totalKapasitas) * 100, 1) : 0;

        $totalHadir = $allStudentsData->sum('total_hadir');
        $totalTerlambat = $allStudentsData->sum('total_terlambat');
        $totalIzin = $allStudentsData->sum('total_izin');
        $totalAlpa = 0;
        foreach ($allStudentsData as $student) {
            $hariAktif = $this->getAktifDaysCount($this->startDate, $this->endDate);
            $alpaSiswa = max(0, $hariAktif - ($student->total_hadir + $student->total_terlambat + $student->total_izin));
            $totalAlpa += $alpaSiswa;
        }
        // Dispatch untuk Chart
        $this->dispatch('statsUpdated', [
            $totalHadir,
            $totalTerlambat,
            $totalIzin,
            $totalAlpa
        ]);

        $siswaRajin = $allStudentsData->filter(fn($s) => $s->total_hadir >= $hariEfektif)->first();
        $siswaTerlambat = $allStudentsData->where('total_terlambat', '>', 0)->sortByDesc('total_terlambat')->first();

        return view('livewire.attendance-report', [
            'recapData' => $baseQuery->paginate(10), // Gunakan query yang sudah difilter
            'classes' => $classes,
            'summaryDate' => $this->getFormattedRange(),
            'hariEfektif' => $hariEfektif,
            'stats' => [
                'rata_rata' => $rataRataHadir,
                'siswa_rajin' => $siswaRajin?->name ?? 'Tidak ada',
                'siswa_terlambat' => $siswaTerlambat ? $siswaTerlambat->name . " ({$siswaTerlambat->total_terlambat}x)" : 'Tidak ada',
                'total_siswa' => $allStudentsData->count(),
                'total_hadir' => $totalHadir,
                'total_terlambat' => $totalTerlambat,
                'total_izin' => $totalIzin,
                'total_alpa' => $totalAlpa,
            ]
        ])->layout('layouts.app');
    }

    private function getFormattedRange()
    {
        $start = Carbon::parse($this->startDate)->translatedFormat('d F Y');
        $end = Carbon::parse($this->endDate)->translatedFormat('d F Y');
        return "$start s/d $end";
    }

    public function exportExcel()
    {
        $fileName = 'rekap_absen_' . $this->startDate . '_ke_' . $this->endDate . '.xlsx';

        return Excel::download(
            new AttendanceExport($this->startDate, $this->endDate, $this->class_name_search),
            $fileName
        );
    }

    public function showStudentDetail($studentId)
    {
        // Ambil data kehadiran siswa berdasarkan range tanggal yang dipilih
        $this->selectedStudentDetail = \App\Models\User::with([
            'attendances' => function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($studentId);

        $this->isDetailModalOpen = true;
    }

    private function getAktifDaysCount($startDate, $endDate)
    {
        // Menggunakan tabel school_calendars untuk menghitung hari efektif
        return \App\Models\SchoolCalendar::whereBetween('date', [$startDate, $endDate])
            ->where('is_holiday', false)
            ->count();
    }


}