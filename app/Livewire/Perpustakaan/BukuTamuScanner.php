<?php

namespace App\Livewire\Perpustakaan;

use App\Models\User;
use App\Models\LibraryVisit;
use Livewire\Component;
use Carbon\Carbon;

class BukuTamuScanner extends Component
{
    // Properti untuk Input RFID (Tersembunyi tapi Autofocus)
    public $searchRfid = '';

    // Properti untuk Menampilkan Data Terakhir di-Tap
    public $lastTap = null;

    // Properti untuk Pesan Sukses/Error di UI
    public $message = '';
    public $status = '';

    /**
     * Listener saat input RFID di-update (saat scanner RFID menginputkan teks)
     */
    public function updatedSearchRfid()
    {
        $this->processVisit();
    }

    /**
     * Logika Utama Pencatatan Kunjungan
     */
    public function processVisit()
    {
        // 1. Trim input RFID
        $rfidInput = trim($this->searchRfid);
        $today = Carbon::today();
        $now = Carbon::now();

        $this->message = '';
        $this->status = '';

        // 2. Jika input kosong, abaikan
        if (empty($rfidInput))
            return;

        // 3. Cari User berdasarkan RFID dan Status Aktif
        $user = User::where('rfid_uid', $rfidInput)
            ->where('is_active', true)
            ->first();

        // 4. Jika Kartu Tidak Terdaftar
        if (!$user) {
            $this->lastTap = null;
            $this->message = "Kartu Tidak Terdaftar! Silakan hubungi Pustakawan.";
            $this->status = "error";
            $this->searchRfid = '';
            return;
        }

        // 5. Cek apakah user sudah tap di perpus HARI INI
        $existingVisit = LibraryVisit::where('user_id', $user->id)
            ->where('visit_date', $today->toDateString())
            ->first();

        if ($existingVisit) {
            // --- MODIFIKASI: CEGAH UPDATE WAKTU ---

            // JANGAN lakukan: $existingVisit->update([...]);
            // Kita biarkan data 'visit_time' asli tetap terjaga (misal 08:00)

            // Tampilkan info tanpa pesan error, tapi infokan sudah tercatat
            // Set ke data asli untuk preview
            $this->lastTap = LibraryVisit::with(['user.class'])->find($existingVisit->id);

            // Ubah pesan agar lebih tepat
            $this->message = "Kunjungan Anda sudah tercatat hari ini, " . $user->name;
            $this->status = "info";

        } else {
            // 6. JIKA BELUM TAP HARI INI -> CATAT BARU

            $newVisit = LibraryVisit::create([
                'user_id' => $user->id,
                'visit_date' => $today->toDateString(),
                'visit_time' => $now->toTimeString(),
                'purpose' => 'belajar', // Default
            ]);

            // Eager load untuk menampilkan Nama & Kelas di UI
            $this->lastTap = LibraryVisit::with(['user.class'])->find($newVisit->id);
            $this->message = "Selamat Datang di Perpustakaan, " . $user->name;
            $this->status = "success";
        }

        // 7. Reset input field untuk scan berikutnya
        $this->searchRfid = '';
    }

    public function render()
    {
        $today = Carbon::today();

        // Ambil 5 kunjungan terbaru hari ini
        $recentVisits = LibraryVisit::with(['user.class'])
            ->where('visit_date', $today->toDateString())
            ->latest('visit_time')
            ->take(5)
            ->get();

        // Hitung total pengunjung perpus hari ini
        $totalVisitors = LibraryVisit::where('visit_date', $today->toDateString())->count();

        // --- MODIFIKASI: Gunakan Layout Kios Terang Baru ---
        // Layout ini tidak membutuhkan auth, jadi konten akan muncul
        return view('livewire.perpustakaan.buku-tamu-scanner', [
            'recentVisits' => $recentVisits,
            'totalVisitors' => $totalVisitors,
        ])->layout('layouts.kios-terang'); // <--- Ganti di sini
    }
}