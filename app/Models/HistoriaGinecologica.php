<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriaGinecologica extends Model
{
    use HasFactory;
    protected $table = 'historia_ginecologica';
    protected $fillable = [
        'historia_clinica_id',
        'fum',
        'gesta',
        'para',
        'ciclo_menstrual',
        'ultimo_pap',
        'notas_eco_mamaria',
    ];
    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_clinica_id');
    }
}