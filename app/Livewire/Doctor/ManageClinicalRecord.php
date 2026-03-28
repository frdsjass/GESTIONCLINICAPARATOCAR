<?php

namespace App\Livewire\Doctor;

use App\Models\Cita;
use App\Models\HistoriaClinica;
use Livewire\Component;

class ManageClinicalRecord extends Component
{
    public Cita $cita;

    // Propiedades del formulario, vinculadas a la historia clínica
    public $sintomas, $diagnostico, $tratamiento, $receta_medica, $observaciones;
    public $peso_kg, $altura_cm, $presion_arterial, $temperatura_c;

    // El método mount() es un constructor que se ejecuta al crear el componente.
    // Aquí recibimos la Cita directamente gracias al "route model binding" de Laravel.
    public function mount(Cita $cita)
    {
        $this->cita = $cita;

        // Carga los datos de la historia clínica si ya existe, o deja los campos vacíos
        $historia = $cita->historiaClinica;
        if ($historia) {
            $this->sintomas = $historia->sintomas;
            $this->diagnostico = $historia->diagnostico;
            $this->tratamiento = $historia->tratamiento;
            $this->receta_medica = $historia->receta_medica;
            $this->observaciones = $historia->observaciones;
            $this->peso_kg = $historia->peso_kg;
            $this->altura_cm = $historia->altura_cm;
            $this->presion_arterial = $historia->presion_arterial;
            $this->temperatura_c = $historia->temperatura_c;
        }
    }

    public function save()
    {
        $this->validate([
            'sintomas' => 'required|string',
            'diagnostico' => 'required|string',
            'tratamiento' => 'required|string',
            'peso_kg' => 'nullable|numeric',
            'altura_cm' => 'nullable|numeric',
            'presion_arterial' => 'nullable|string',
            'temperatura_c' => 'nullable|numeric',
        ]);

        // updateOrCreate busca una historia clínica para esta cita y la actualiza,
        // o crea una nueva si no existe.
        HistoriaClinica::updateOrCreate(
            ['cita_id' => $this->cita->id],
            [
                'paciente_id' => $this->cita->paciente_id,
                'medico_id' => $this->cita->medico_id,
                'sintomas' => $this->sintomas,
                'diagnostico' => $this->diagnostico,
                'tratamiento' => $this->tratamiento,
                'receta_medica' => $this->receta_medica,
                'observaciones' => $this->observaciones,
                'peso_kg' => $this->peso_kg,
                'altura_cm' => $this->altura_cm,
                'presion_arterial' => $this->presion_arterial,
                'temperatura_c' => $this->temperatura_c,
            ]
        );

        // Actualizamos el estado de la cita a "Completada"
        $this->cita->estado = 'Completada';
        $this->cita->save();

        session()->flash('message', '¡Historia Clínica guardada exitosamente!');

        // Redirigimos de vuelta al panel del médico
        return redirect()->route('doctor.dashboard');
    }

    public function render()
    {
        return view('livewire.doctor.manage-clinical-record')
            ->layout('layouts.app');
    }
}