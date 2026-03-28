<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoExamen extends Model
{
    use HasFactory;

    protected $table = 'tipo_examenes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'estado',
    ];
}