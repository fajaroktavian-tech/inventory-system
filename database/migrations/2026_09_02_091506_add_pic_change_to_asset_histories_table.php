<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mengubah enum agar mencakup 'pic_change'
        DB::statement("ALTER TABLE asset_histories MODIFY COLUMN activity_type ENUM('registration', 'room_transfer', 'condition_update', 'pic_change', 'maintenance', 'loan', 'other')");
    }

    public function down(): void
    {
        // Kembalikan ke enum semula jika rollback
        DB::statement("ALTER TABLE asset_histories MODIFY COLUMN activity_type ENUM('registration', 'room_transfer', 'condition_update', 'maintenance', 'loan', 'other')");
    }
};
