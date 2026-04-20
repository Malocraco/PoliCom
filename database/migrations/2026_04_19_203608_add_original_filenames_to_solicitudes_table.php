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
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->string('pieza_creativa_nombre')->nullable()->after('pieza_creativa_path');
            $table->string('base_datos_nombre')->nullable()->after('base_datos_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropColumn(['pieza_creativa_nombre', 'base_datos_nombre']);
        });
    }
};
