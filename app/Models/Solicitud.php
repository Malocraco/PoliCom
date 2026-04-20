<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'numero_solicitud',
        'area_solicitante',
        'cliente_nombre',
        'cliente_email',
        'analista_id',
        'com_id',
        'nombre_envio',
        'tipo',
        'tipo_target',
        'detalle_target',
        'target',
        'tiene_evento_noticia',
        'link_url',
        'subject',
        'mask',
        'sms_copy',
        'fecha_requerida',
        'hora_programada',
        'pieza_creativa_path',
        'pieza_creativa_nombre',
        'base_datos_path',
        'base_datos_nombre',
        'estado',
    ];

    protected $casts = [
        'tiene_evento_noticia' => 'boolean',
        'fecha_requerida' => 'date',
        'hora_programada' => 'string', // Formato HH:mm:ss
    ];

    public function analista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analista_id');
    }
}
