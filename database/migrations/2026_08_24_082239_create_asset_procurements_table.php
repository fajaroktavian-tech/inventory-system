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
        Schema::create('asset_procurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Siapa yang mengajukan
            $table->enum('type', ['aset', 'bhp']); // Pilihan: Aset atau Barang Habis Pakai
            $table->string('item_name'); // Nama barang yang diajukan
            $table->integer('qty'); // Jumlah
            $table->decimal('estimated_price', 15, 2)->nullable(); // Perkiraan harga satuan/total
            $table->text('reason'); // Alasan / Kebutuhan untuk apa
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('admin_note')->nullable(); // Catatan dari sarpras/admin jika ditolak/disetujui
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_procurements');
    }
};
