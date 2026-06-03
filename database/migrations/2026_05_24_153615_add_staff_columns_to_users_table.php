<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'nip')) {
            $table->string('nip')->nullable()->after('username');
        }
        if (!Schema::hasColumn('users', 'position')) {
            $table->string('position')->nullable()->after('role');
        }
        if (!Schema::hasColumn('users', 'phone')) {
            $table->string('phone')->nullable()->after('position');
        }
        if (!Schema::hasColumn('users', 'address')) {
            $table->text('address')->nullable()->after('phone');
        }
        if (!Schema::hasColumn('users', 'avatar')) {
            $table->string('avatar')->nullable()->after('address');
        }
    });
}

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn(['nip', 'position', 'phone', 'address', 'avatar']);
        });
    }
};