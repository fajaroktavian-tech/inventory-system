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
        Schema::table('asset_procurements', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('reason');
        });

        Schema::table('asset_maintenances', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('damage_description');
        });
    }

    public function down(): void
    {
        Schema::table('asset_procurements', function (Blueprint $table) {
            $table->dropColumn('photo');
        });

        Schema::table('asset_maintenances', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};
