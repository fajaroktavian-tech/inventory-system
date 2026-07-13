<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_calendars', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->boolean('is_holiday')->default(false); // true jika libur, false jika sekolah
            $table->string('description')->nullable(); // contoh: "Libur Semester", "Libur Nasional"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_calendars');
    }
};