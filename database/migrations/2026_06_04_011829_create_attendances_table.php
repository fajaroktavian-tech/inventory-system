<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // Relasi ke Siswa (User)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Tanggal Absensi
            $table->date('date');
            
            // Waktu Tap
            $table->time('time_in')->nullable();  // Jam Masuk
            $table->time('time_out')->nullable(); // Jam Pulang
            
            // Status Absensi
            // default 'alpa' agar jika sistem dijalankan pagi hari, siswa yang belum tap otomatis terhitung alpa
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpa', 'dispen'])->default('alpa');
            
            // Catatan (misal: "Izin karena acara keluarga" atau "Sakit DB")
            $table->text('note')->nullable();
            
            // Koordinasi & Verifikasi
            // Siapa yang menginput/memverifikasi (Wali Kelas / Guru Piket / Sistem)
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();

            // Indexing untuk performa pencarian laporan
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};