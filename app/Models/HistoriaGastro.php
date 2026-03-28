<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriaGastro extends Model
{
    use HasFactory;
    protected $table = 'historia_gastro';
    protected $fillable = [
        'historia_clinica_id',
        'resumen_endoscopia',
        'resumen_colonoscopia',
        'prueba_h_pylori',
        'dieta_intolerancias',
    ];
    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_clinica_id');
    }
}