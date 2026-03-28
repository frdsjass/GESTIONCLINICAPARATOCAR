<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lote extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicamento_id',
        'numero_lote',
        'fecha_vencimiento',
        'cantidad',
        'precio_venta',
        'stock_minimo',
    ];

    /**
     * Define la relación: Un Lote pertenece a un Medicamento.
     */
    public function medicamento(): BelongsTo
    {
        return $this->belongsTo(Medicamento::class);
    }
}