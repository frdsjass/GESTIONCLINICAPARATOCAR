<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'medicamento_id',
        'cantidad',
        'precio_unitario_en_venta',
        'subtotal',
    ];

    // El detalle de una venta pertenece a un Medicamento
    public function medicamento(): BelongsTo
    {
        return $this->belongsTo(Medicamento::class);
    }
}