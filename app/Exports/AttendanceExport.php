<?php

namespace App\Exports;

use App\Models\User;
use App\Models\SchoolCalendar;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $className;

    /**
     * Constructor untuk menerima parameter filter dari komponen Livewire.
     *
     * @param string $startDate Tanggal mulai (Y-m-d)
     * @param string $endDate Tanggal selesai (Y-m-d)
     * @param string|null $className Nama kelas yang difilter (opsional)
     */
    public function __construct($startDate, $endDate, $className = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->className = $className;
    }
    public function query()
    {
        return User::query()
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
            ])
            ->when($this->className, function ($query) {
                $query->whereHas('class', function ($subQuery) {
                    $subQuery->where('name', $this->className);
                });
            })

            ->orderBy('name');
    }

    /**
     * Menentukan judul header untuk file Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama Siswa',
            'NIS',
            'Kelas',
            'Total Hadir',
            'Total Terlambat',
            'Total Izin/Sakit',
            'Total Alpa',
            'Efektivitas (%)',
        ];
    }

    /**
     * Memetakan setiap baris data dari query ke kolom yang sesuai di Excel.
     *
     * @param mixed $row Satu baris hasil query (instance App\Models\User)
     * @return array
     */
    public function map($row): array
    {
        // 1. Hitung jumlah hari efektif (hari sekolah) dalam periode ini
        // Mengambil data dari tabel school_calendars
        $hariEfektif = SchoolCalendar::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('is_holiday', false)
            ->count();

        // 2. Hitung total hari siswa masuk (Hadir + Terlambat + Izin)
        $totalMasuk = $row->total_hadir + $row->total_terlambat + $row->total_izin;

        // 3. Hitung Alpa (Hari efektif dikurangi total masuk, tidak boleh negatif)
        $alpa = max(0, $hariEfektif - $totalMasuk);

        // 4. Hitung persentase kehadiran
        $persentase = $hariEfektif > 0
            ? round(($totalMasuk / $hariEfektif) * 100)
            : 0;

        // 5. Kembalikan array data yang sudah dipetakan sesuai headings
        return [
            $row->name,
            $row->nis,
            $row->class->name ?? '-', // Tampilkan nama kelas, atau '-' jika tidak ada
            $row->total_hadir,
            $row->total_terlambat,
            $row->total_izin,
            $alpa, // Nilai alpa yang dihitung secara dinamis
            $persentase . '%', // Tambahkan simbol %
        ];
    }

    /**
     * Menentukan judul sheet atau nama file default saat diexport.
     *
     * @return string
     */
    public function title(): string
    {
        $formattedStart = Carbon::parse($this->startDate)->format('d-m-Y');
        $formattedEnd = Carbon::parse($this->endDate)->format('d-m-Y');

        $title = 'Rekap_Absen_' . $formattedStart . '_s.d._' . $formattedEnd;

        if ($this->className) {
            $title .= '_Kelas_' . $this->className;
        }

        // Batasi panjang judul maksimal 31 karakter (batasan Excel)
        return \Illuminate\Support\Str::limit($title, 31, '');
    }
}