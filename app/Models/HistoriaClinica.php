<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HistoriaClinica extends Model
{
    use HasFactory;

    // Asegúrate de que tu tabla se llame 'historias_clinicas'
    protected $table = 'historias_clinicas';

    protected $fillable = [
        'cita_id',
        'paciente_id',
        'medico_id',
        'sintomas',
        'diagnostico',
        'tratamiento',
        'receta_medica',
        'observaciones',
        'peso_kg',
        'altura_cm',
        'presion_arterial',
        'temperatura_c',
    ];

    // --- ¡SOLUCIÓN AL ERROR! ---
    /**
     * The attributes that should be cast.
     * Le dice a Eloquent que trate estas columnas como objetos de fecha (Carbon).
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    // --- FIN DE LA SOLUCIÓN ---

    // Relación: Una historia pertenece a un Paciente
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    // Relación: Una historia pertenece a un Médico
    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }

    // Relación: Una historia pertenece a una Cita
    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    // Una entrada de historial común TIENE UN (opcional) registro pediátrico.
    public function historiaPediatrica(): HasOne
    {
        return $this->hasOne(HistoriaPediatrica::class, 'historia_clinica_id');
    }

    // --- ¡NUEVAS RELACIONES AÑADIDAS! ---

    // Una entrada de historial TIENE UN (opcional) registro ginecológico.
    public function historiaGinecologica(): HasOne
    {
        return $this->hasOne(HistoriaGinecologica::class, 'historia_clinica_id');
    }

    // Una entrada de historial TIENE UN (opcional) registro cardiológico.
    public function historiaCardiologica(): HasOne
    {
        return $this->hasOne(HistoriaCardiologica::class, 'historia_clinica_id');
    }

    // Una entrada de historial TIENE UN (opcional) registro traumatológico.
    public function historiaTraumatologica(): HasOne
    {
        return $this->hasOne(HistoriaTraumatologica::class, 'historia_clinica_id');
    }

    // Una entrada de historial TIENE UN (opcional) registro de gastroenterología.
    public function historiaGastro(): HasOne
    {
        return $this->hasOne(HistoriaGastro::class, 'historia_clinica_id');
    }
    
    // --- FIN DE NUEVAS RELACIONES ---
}