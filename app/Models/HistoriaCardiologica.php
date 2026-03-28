<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriaCardiologica extends Model
{
    use HasFactory;
    protected $table = 'historia_cardiologica';
    protected $fillable = [
        'historia_clinica_id',
        'riesgo_cardiovascular',
        'resumen_ecg',
        'resumen_ecocardiograma',
        'colesterol_total',
        'hdl',
        'ldl',
        'trigliceridos',
    ];
    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_clinica_id');
    }
}