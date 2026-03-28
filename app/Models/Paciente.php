<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nombre_completo',
        'carnet_identidad',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'direccion',
        'email',
        'antecedentes_medicos',
        'alergias',
        'estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }


    public function historialesClinicos(): HasMany
    {
        return $this->hasMany(HistoriaClinica::class, 'paciente_id');
    }

    public function isProfileComplete(): bool
    {
        if (is_null($this->direccion) || 
            is_null($this->antecedentes_medicos) || 
            is_null($this->alergias)) {
            return false;
        }
        return true;
    }
}
