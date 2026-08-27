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
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Pelapor
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete(); // Barang / Aset yang rusak (ambil dari tabel assets Anda)
            $table->text('damage_description'); // Deskripsi kerusakan
            $table->enum('status', ['pending', 'process', 'repaired', 'replaced'])->default('pending'); 
            // pending: Menunggu cek, process: Sedang diperbaiki, repaired: Selesai diperbaiki, replaced: Diganti baru
            $table->text('repair_note')->nullable(); // Catatan teknisi/sarpras
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
