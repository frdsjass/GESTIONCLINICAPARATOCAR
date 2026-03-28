<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriaTraumatologica extends Model
{
    use HasFactory;
    protected $table = 'historia_traumatologica';
    protected $fillable = [
        'historia_clinica_id',
        'tipo_lesion',
        'zona_afectada',
        'rango_movilidad',
        'reflejos',
        'resumen_pruebas_imagen',
    ];
    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_clinica_id');
    }
}