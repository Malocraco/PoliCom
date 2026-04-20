<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactoGeneral extends Model
{
    use HasFactory;

    protected $table = 'contactos_generales';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'segmento',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
