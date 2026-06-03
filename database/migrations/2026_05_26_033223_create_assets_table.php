<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Master Tipe Aset (General info)
        Schema::create('asset_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name'); // misal: Laptop Asus Expertbook
            $table->string('brand')->nullable();
            $table->text('specification')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Unit Aset Spesifik (Fisik Barang)
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_item_id')->constrained('asset_items')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms'); // Lokasi
            $table->foreignId('pic_id')->constrained('users'); // Penanggung Jawab (Guru/Staff)
            $table->string('serial_number')->unique()->nullable();
            $table->string('source_fund')->nullable(); // BOS, BOPD, dll
            $table->year('acquisition_year')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->enum('condition', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->enum('status', ['tersedia', 'dipinjam', 'hilang', 'diserahkan'])->default('tersedia');
            $table->string('barcode_token')->unique()->nullable();
            $table->timestamps();
        });

        // 3. Tabel Transaksi Peminjaman
        Schema::create('asset_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('user_id')->constrained('users'); // Peminjam (Siswa/Guru)
            $table->date('loan_date');
            $table->date('due_date')->nullable();
            $table->date('return_date')->nullable();
            $table->enum('status', ['active', 'returned', 'late'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
