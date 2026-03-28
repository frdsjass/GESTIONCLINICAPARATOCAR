<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// --- 1. AÑADIDO ---
use App\Models\MedicoHorario;

class Medico extends Model
{
    // use HasFactory; // <- Esto estaba duplicado, lo quito.
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'especialidad_id',
        'cedula_profesional',
        'telefono',
        'estado',
        'direccion',
        'limite_citas_dia',
    ];

    // Un Medico pertenece a una Especialidad
    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id');
    }

    // Un Medico pertenece a un Usuario (para el login)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Un Medico tiene muchas Citas
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'medico_id');
    }

    // --- 2. FUNCIÓN AÑADIDA PARA SOLUCIONAR EL ERROR ---
    /**
     * Un médico puede tener múltiples rangos de horarios.
     */
    public function horarios(): HasMany
    {
        return $this->hasMany(MedicoHorario::class, 'medico_id');
    }
    // ---------------------------------
}