<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenLaboratorio extends Model
{
    use HasFactory;

    /**
     * ¡LA CORRECCIÓN MÁS IMPORTANTE!
     * * Por defecto, Laravel buscaría la tabla 'orden_laboratorio' (singular).
     * Nuestras migraciones crean 'orden_laboratorios' (plural).
     * Esta línea le dice al modelo que use el nombre de tabla correcto.
     */
    protected $table = 'ordenes_laboratorios';

    /**
     * ¡SEGUNDA CORRECCIÓN!
     * Añadimos los nuevos campos de la base de datos a la lista $fillable
     * para que podamos guardarlos desde el componente de Livewire.
     */
    protected $fillable = [
        'paciente_id',
        'medico_id',
        'cita_id',
        'estado',
        'notas_medico',
        
        // --- CAMPOS NUEVOS AÑADIDOS ---
        'resultados_texto',
        'resultado_pdf_path',
        // --- FIN DE CAMPOS NUEVOS ---
    ];

    // Relaciones (Estas ya estaban bien)
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    // Una orden tiene muchos exámenes (detalles)
    public function detalles(): HasMany
    {
        return $this->hasMany(OrdenExamenDetalle::class, 'orden_laboratorio_id');
    }
}