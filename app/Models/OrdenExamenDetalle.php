<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenExamenDetalle extends Model
{
    use HasFactory;

    protected $table = 'orden_examen_detalle';

    protected $fillable = [
        'orden_laboratorio_id',
        'tipo_examen_id',
        'resultados',
        'notas_laboratorista',
        'valores_referencia',
        'metodologia',
    ];

    protected $casts = [
        'resultados' => 'array', // Si almacenas JSON
        'valores_referencia' => 'array', // Si almacenas JSON
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con la orden de laboratorio
     */
    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenLaboratorio::class, 'orden_laboratorio_id');
    }

    /**
     * Relación con el tipo de examen
     */
    public function tipoExamen(): BelongsTo
    {
        return $this->belongsTo(TipoExamen::class, 'tipo_examen_id');
    }
}