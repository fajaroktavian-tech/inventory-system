<?php

namespace App\Livewire\Absensi;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Schedule;
use App\Models\SchoolCalendar;

class AttendanceGateway extends Component
{
    public $searchRfid = '';
    public $lastTap = null;
    public $message = '';
    public $status = '';

    public function updatedSearchRfid()
    {
        $this->processAttendance();
    }

    public function processAttendance()
    {
        // Trim input untuk menghindari spasi atau karakter tak terlihat dari scanner
        $rfidInput = trim($this->searchRfid);
        $today = Carbon::today();

        // 1. CEK HARI LIBUR DARI DATABASE (Cukup satu kali di sini)
        $isHoliday = SchoolCalendar::where('date', $today->toDateString())
            ->where('is_holiday', true)
            ->exists();

        if ($isHoliday) {
            $this->message = "Hari ini libur. Absensi dinonaktifkan.";
            $this->status = "error";
            $this->searchRfid = ''; // Penting agar input kosong kembali
            $this->dispatch('message-updated');
            $this->dispatch('play-sound', status: 'error');
            return;
        }

        if (empty($rfidInput))
            return;

        $this->message = '';
        $this->status = '';

        // 2. Cari User berdasarkan RFID
        $user = User::where('rfid_uid', $rfidInput)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            $this->lastTap = null;
            $this->message = "Kartu Tidak Terdaftar! Silakan hubungi petugas.";
            $this->status = "error";
            $this->searchRfid = '';
            $this->dispatch('play-sound', status: 'error');
            $this->dispatch('message-updated');
            return;
        }

        $today = Carbon::today();
        $now = Carbon::now();
        $activeSchedule = Schedule::where('is_active', true)->first();

        // Gunakan jadwal dari DB, jika tidak ada jadwal aktif, gunakan default jam 12:00
        $startTime = $activeSchedule ? $activeSchedule->start_time : '06:45:00';
        $endTime = $activeSchedule ? $activeSchedule->end_time : '14:00:00';

        // 2. Cek apakah sudah ada data absen hari ini
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Hitung limit terlambat (misal: start_time + 15 menit)
        $limitTime = Carbon::parse($startTime)->addMinutes(15)->toTimeString();

        if (!$attendance) {
            // LOGIKA TAP IN (MASUK)
            $attendanceStatus = 'hadir';

            if ($now->toTimeString() > $limitTime) {
                $attendanceStatus = 'terlambat';
            }

            // Simpan ke database
            $newAttendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'time_in' => $now->toTimeString(),
                'status' => $attendanceStatus,
            ]);

            // Eager load untuk menampilkan nama & kelas di UI
            $this->lastTap = Attendance::with(['student.class'])->find($newAttendance->id);
            $this->message = "Selamat Datang, " . $user->name;
            $this->status = "success";

        } else {
            // LOGIKA TAP OUT (PULANG)
            if ($now->toTimeString() < $endTime) {
                $this->lastTap = $attendance;
                $this->message = "Belum jam pulang (Jam pulang: " . substr($endTime, 0, 5) . "), " . $user->name . "!";
                $this->status = "error"; // Ganti ke error agar warna UI jadi merah/warning
            } else {
                $attendance->update([
                    'time_out' => $now->toTimeString(),
                ]);
                $this->lastTap = $attendance;
                $this->message = "Hati-hati di jalan, " . $user->name;
                $this->status = "success";
            }
        }

        // Reset input field untuk scan berikutnya
        $this->searchRfid = '';
        $this->dispatch('play-sound', status: $this->status);
        $this->dispatch('message-updated');
    }

    public function render()
    {
        $today = Carbon::today();
        $isHoliday = SchoolCalendar::where('date', $today->toDateString())
            ->where('is_holiday', true)
            ->exists();
        $recentTaps = Attendance::with(['student.class'])
            ->where('date', $today)
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('livewire.attendance-gateway', [
            'recentTaps' => $recentTaps,
            'isHoliday' => $isHoliday // Kirim variabel ini ke view
        ])->layout('layouts.kios');
    }
}