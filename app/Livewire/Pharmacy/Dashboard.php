<?php

namespace App\Livewire\Pharmacy;

use Livewire\Component;
use App\Models\Medicamento; // Para buscar medicamentos
use App\Models\Lote; // Para buscar lotes
use Illuminate\Support\Facades\DB; // Para usar sumas y agrupaciones

class Dashboard extends Component
{
    public function render()
    {
        // Buscamos medicamentos con stock bajo
        // 1. Sumamos la cantidad total de todos los lotes de cada medicamento
        // 2. Comparamos esa suma con el stock_minimo (usamos el mínimo stock_minimo si hay varios lotes)
        // 3. Filtramos solo aquellos donde la cantidad total sea menor o igual al stock_minimo
        $productosBajoStock = Medicamento::with('lotes') // Cargamos los lotes
            ->whereHas('lotes', function ($query) {
                // Aseguramos que solo consideramos medicamentos que *tienen* lotes
            })
            ->get() // Obtenemos todos los medicamentos con lotes
            ->filter(function ($medicamento) {
                // Calculamos el stock total sumando la cantidad de todos sus lotes
                $stockTotal = $medicamento->lotes->sum('cantidad');
                // Obtenemos el stock mínimo más bajo definido entre sus lotes (o 0 si no hay)
                $stockMinimo = $medicamento->lotes->min('stock_minimo') ?? 0;
                 // Devolvemos true solo si el stock total es menor o igual al mínimo (y mayor que -1 para evitar errores)
                return $stockTotal <= $stockMinimo && $stockTotal >= 0;
            });


        // Pasamos los datos a la vista
        return view('livewire.pharmacy.dashboard', compact('productosBajoStock'))
               ->layout('layouts.app'); // Usamos el layout principal
    }
}
