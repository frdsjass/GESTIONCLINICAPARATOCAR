<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_comercial',
        'principio_activo',
        'laboratorio',
        'presentacion',
        'requiere_receta',
    ];

    /**
     * Define la relación: Un Medicamento tiene muchos Lotes.
     */
    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class);
    }

    /**
     * Un "accesor" para calcular el stock total sumando las cantidades de todos sus lotes.
     */
    public function getStockTotalAttribute()
    {
        return $this->lotes()->sum('cantidad');
    }
}