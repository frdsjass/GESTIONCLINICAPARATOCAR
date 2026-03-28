<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Cita;
use App\Models\Venta;
use App\Models\Lote; 
use Carbon\Carbon;

// ====== ¡AÑADIDOS! ======
use Illuminate\Support\Facades\Auth;
use App\Models\OrdenLaboratorio; 

class DashboardHome extends Component
{
    
    // --- Stats de Admin ---
    public $totalPacientes;
    public $totalMedicos;
    public $citasHoy;
    public $ingresosHoy;
    public $citasPendientes;
    public $stockBajo;

    // --- ¡NUEVO! Stats de Laboratorista ---
    public $stats = [];


    public function mount()
    {
        // ====== ¡MODIFICADO! ======
        $user = Auth::user();

        // --- 1. Cargar Stats de Laboratorista (si tiene el rol) ---
        if ($user->hasRole('Laboratorista')) {
            $this->stats = [
                'pendientes' => OrdenLaboratorio::where('estado', 'Pendiente')->count(),
                'completadasHoy' => OrdenLaboratorio::where('estado', 'Resultados Listos')
                                    ->whereDate('updated_at', Carbon::today())
                                    ->count(),
                'totalHoy' => OrdenLaboratorio::whereDate('created_at', Carbon::today())
                                    ->count(),
            ];
        }

        // --- 2. Cargar Stats de Admin (si tiene el rol) ---
        // (Se asume que el Admin/Recepcionista ve esto)
        if ($user->hasRole('Admin|Recepcion')) { 
            // (Esta es tu lógica original)
            $this->totalPacientes = Paciente::where('estado', 'Activo')->count();
            $this->totalMedicos = Medico::where('estado', 'Activo')->count();
            
            $today = Carbon::today();
            
            $this->citasHoy = Cita::whereDate('fecha_hora_inicio', $today)
                                ->whereIn('estado', ['Programada', 'Confirmada', 'Completada'])
                                ->count();
                                
            $this->ingresosHoy = Venta::whereDate('created_at', $today)
                                    ->where('estado', 'Completada') 
                                    ->sum('monto_total');

            // cargar las tablas de accion rapida
            
            // citas no completas
            $this->citasPendientes = Cita::with('paciente', 'medico.user')
                                        ->whereDate('fecha_hora_inicio', $today)
                                        ->whereIn('estado', ['Programada', 'Confirmada'])
                                        ->orderBy('fecha_hora_inicio')
                                        ->take(5) // Limitar a las 5 próximas
                                        ->get();
                                        
            // Alertas de stock bajo
            if (class_exists(\App\Models\Lote::class)) {
                $this->stockBajo = Lote::with('medicamento')
                                        ->whereColumn('cantidad', '<=', 'stock_minimo')
                                        ->where('cantidad', '>', 0) 
                                        ->orderBy('cantidad', 'asc')
                                        ->take(5) 
                                        ->get();
            } else {
                $this->stockBajo = collect(); 
            }
        }
        // ====== FIN DE LA MODIFICACIÓN ======
    }

    public function render()
    {
        return view('livewire.admin.dashboard-home')
                ->layout('layouts.app');
    }
}