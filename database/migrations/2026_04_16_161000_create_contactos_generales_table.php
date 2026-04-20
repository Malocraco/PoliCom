<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contactos_generales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('segmento')->nullable();
            $table->json('metadata')->nullable(); // Para campos extra como ciudad, programa, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos_generales');
    }
};
