<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Especialidad extends Model
{
    use HasFactory;
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'especialidades'; // <-- ESTA ES LA LÍNEA MÁGICA

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    // Una Especialidad tiene muchos Medicos
    public function medicos(): HasMany
    {
        return $this->hasMany(Medico::class, 'especialidad_id');
    }
}