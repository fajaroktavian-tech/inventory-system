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
        Schema::create('asset_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Petugas yang mencatat aksi
            
            // Jenis aktivitas/kejadian pada siklus aset
            $table->enum('activity_type', [
                'registration',     // Saat pertama kali dicatat/registrasi unit
                'room_transfer',    // Perpindahan ruangan / mutasi
                'condition_update', // Perubahan kondisi (baik, rusak ringan, rusak berat)
                'maintenance',      // Masuk ke perbaikan
                'loan',             // Peminjaman / pengembalian
                'other'             // Catatan umum / lainnya
            ]);

            $table->string('title'); // Judul singkat aktivitas, misal: "Pindah Ruangan" atau "Perubahan Kondisi"
            $table->text('description')->nullable(); // Keterangan detail kejadian
            
            // Opsional untuk melacak perubahan data jika diperlukan
            $table->string('old_value')->nullable(); 
            $table->string('new_value')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_histories');
    }
};
