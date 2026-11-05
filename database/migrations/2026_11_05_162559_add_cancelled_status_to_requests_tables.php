<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE asset_procurements MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");

        // 2. Ubah juga tabel asset_maintenances (Laporan Perbaikan) agar konsisten
        // Saya berasumsi tabel perbaikan juga ENUM yang sama
        DB::statement("ALTER TABLE asset_maintenances MODIFY COLUMN status ENUM('pending', 'process', 'repaired', 'replaced', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('asset_procurements')->where('status', 'cancelled')->update(['status' => 'pending']);
        DB::table('asset_maintenances')->where('status', 'cancelled')->update(['status' => 'pending']);

        // Kembalikan tabel asset_procurements ke awal
        DB::statement("ALTER TABLE asset_procurements MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending'");

        // Kembalikan tabel asset_maintenances ke awal (Asumsi ENUM awal Perbaikan)
        DB::statement("ALTER TABLE asset_maintenances MODIFY COLUMN status ENUM('pending', 'process', 'repaired', 'replaced', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};
