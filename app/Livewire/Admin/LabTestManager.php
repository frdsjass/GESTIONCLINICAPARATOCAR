<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\TipoExamen;
use Livewire\WithPagination;

class LabTestManager extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $nombre, $descripcion, $precio, $estado;
    public $examen_id;

    public $isOpen = false;
    public $search = '';

    // ¡AÑADIDO! Propiedad para filtrar
    public $showInactive = false;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'precio' => 'nullable|numeric|min:0',
        'estado' => 'required|in:Activo,Inactivo',
    ];

    public function render()
    {
        // ¡MODIFICADO! Query base para filtrar por estado
        $examenesQuery = TipoExamen::where('nombre', 'like', '%' . $this->search . '%');
            
        // ¡AÑADIDO! Lógica para mostrar/ocultar inactivos
        $examenesQuery->when(!$this->showInactive, function ($query) {
            return $query->where('estado', 'Activo');
        });

        $examenes = $examenesQuery->orderBy('id', 'desc')->paginate(10);

        return view('livewire.admin.lab-test-manager', [
            'examenes' => $examenes,
        ])->layout('layouts.app'); // Usa tu layout principal
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->reset(['nombre', 'descripcion', 'precio', 'estado', 'examen_id']);
        $this->estado = null; // ¡MODIFICADO! Valor por defecto a null
    }

    public function store()
    {
        $this->validate();

        TipoExamen::updateOrCreate(['id' => $this->examen_id], [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'estado' => $this->estado,
        ]);

        session()->flash('message', 
            $this->examen_id ? 'Tipo de Examen actualizado.' : 'Tipo de Examen creado.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $examen = TipoExamen::findOrFail($id);
        $this->examen_id = $id;
        $this->nombre = $examen->nombre;
        $this->descripcion = $examen->descripcion;
        $this->precio = $examen->precio;
        $this->estado = $examen->estado;
        $this->openModal();
    }

    // ¡MODIFICADO! Renombrada de delete() a toggleEstado() y lógica actualizada
    public function toggleEstado($id)
    {
        $examen = TipoExamen::find($id);
        if ($examen) {
            // Lógica de toggle
            $examen->estado = ($examen->estado == 'Activo') ? 'Inactivo' : 'Activo';
            $examen->save();
            session()->flash('message', 'Estado del examen actualizado.');
        }
    }
}