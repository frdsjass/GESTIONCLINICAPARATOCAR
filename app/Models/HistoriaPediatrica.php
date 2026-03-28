<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriaPediatrica extends Model
{
    use HasFactory;

    // Definimos la tabla porque el nombre es irregular
    protected $table = 'historia_pediatrica';

    // Campos que podemos llenar masivamente
    protected $fillable = [
        'historia_clinica_id',
        'perimetro_cefalico_cm',
        'esquema_vacunacion',
        'desarrollo_psicomotor',
        'alimentacion',
    ];

    // Relación inversa: Un registro pediátrico pertenece a UN historial clínico
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_clinica_id');
    }
}