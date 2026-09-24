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
        Schema::table('asset_maintenances', function (Blueprint $table) {
            // Ubah asset_id agar boleh kosong (nullable) untuk kasus kerusakan fasilitas umum
            $table->foreignId('asset_id')->nullable()->change();
            
            // Tambahkan kolom untuk fasilitas non-aset & lokasi ruangan
            $table->string('facility_name')->nullable()->after('user_id'); // Contoh: "Pipa Air Bocor / Korsleting Listrik"
            $table->foreignId('room_id')->nullable()->after('facility_name')->constrained('rooms')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('asset_maintenances', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn(['facility_name', 'room_id']);
            $table->foreignId('asset_id')->nullable(false)->change();
        });
    }
};
