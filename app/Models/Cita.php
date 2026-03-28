<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

// --- ¡AÑADIDO! ---
// Necesitamos importar el modelo que vamos a relacionar
use App\Models\OrdenLaboratorio;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'fecha_hora_inicio',
        'fecha_hora_fin',
        'motivo_consulta',
        'estado',
        'notas_recepcion',
    ];

    // --- ¡SOLUCIÓN PREVENTIVA AL ERROR! ---
    /**
     * The attributes that should be cast.
     * Le dice a Eloquent que trate estas columnas como objetos de fecha (Carbon).
     *
     * @var array
     */
    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_fin' => 'datetime', // <-- Importante añadir esta también
    ];
    // --- FIN DE LA SOLUCIÓN ---


    // Una Cita pertenece a un Paciente
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    // Una Cita pertenece a un Medico
    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }

    // Una Cita tiene una Historia Clinica
    public function historiaClinica(): HasOne
    {
        return $this->hasOne(HistoriaClinica::class, 'cita_id');
    }

    // --- ¡AQUÍ ESTÁ LA SOLUCIÓN (FASE N)! ---
    /**
     * Una Cita puede tener (o no) una Orden de Laboratorio asociada.
     */
    public function ordenLaboratorio(): HasOne
    {
        // El 'cita_id' es la llave foránea en la tabla 'ordenes_laboratorios'
        return $this->hasOne(OrdenLaboratorio::class, 'cita_id');
    }
}