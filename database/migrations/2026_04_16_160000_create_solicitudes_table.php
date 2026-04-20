<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_solicitud')->unique();
            $table->string('area_solicitante');
            $table->string('cliente_nombre');
            $table->string('cliente_email');
            $table->foreignId('analista_id')->constrained('users')->onDelete('cascade');
            $table->string('com_id');
            $table->string('nombre_envio');
            $table->enum('tipo', ['Email', 'SMS']);
            $table->string('target');
            $table->boolean('tiene_evento_noticia')->default(false);
            $table->string('link_url')->nullable();
            $table->string('subject')->nullable(); // Solo para Email
            $table->string('mask');
            $table->text('sms_copy')->nullable(); // Solo para SMS
            $table->date('fecha_requerida');
            $table->time('hora_programada')->nullable();
            $table->string('pieza_creativa_path')->nullable();
            $table->string('base_datos_path')->nullable();
            $table->enum('estado', [
                'creada', 
                'borrador_infobip', 
                'prueba_enviada', 
                'aprobada', 
                'con_cambios', 
                'cancelada', 
                'programada', 
                'lanzada'
            ])->default('creada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
