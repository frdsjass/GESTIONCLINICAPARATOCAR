<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- Añadido
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory; // <-- Añadido

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'personal'; // <-- Le decimos que use la tabla 'personal'

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ // <-- Los campos que definimos en la migración
        'user_id',
        'carnet_identidad',
        'telefono',
        'direccion',
        'estado',
    ];

    /**
     * Relación: Un registro de Personal "pertenece a" un User.
     * Esto nos permite hacer: $personal->user->name
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

