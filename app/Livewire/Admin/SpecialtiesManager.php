<?php

namespace App\Livewire\Admin;

use App\Models\Especialidad;
use Livewire\Component;
use Livewire\WithPagination;

class SpecialtiesManager extends Component
{
    use WithPagination;

    public $nombre, $descripcion;
    public $estado; 
    public $specialty_id;
    public $isOpen = false;
    public $search = '';
    
    public $showInactive = false;

    protected $listeners = ['render' => 'render'];

    public function render()
    {
        $query = Especialidad::query();

        if (!$this->showInactive) {
            $query->where('estado', 'Activo');
        }

        if (!empty($this->search)) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        $especialidades = $query->latest()->paginate(5);
        
        return view('livewire.admin.specialties-manager', compact('especialidades'))
            ->layout('layouts.app');
    }

    public function toggleEstado($id)
    {
        $especialidad = Especialidad::find($id);
        if ($especialidad) {
            $especialidad->estado = ($especialidad->estado == 'Activo') ? 'Inactivo' : 'Activo';
            $especialidad->save();
            session()->flash('message', 'Estado de la especialidad actualizado.');
            $this->dispatch('render'); 
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function edit($id)
    {
        $especialidad = Especialidad::findOrFail($id);
        $this->specialty_id = $id;
        $this->nombre = $especialidad->nombre;
        $this->descripcion = $especialidad->descripcion;
        $this->estado = $especialidad->estado; 
        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'nombre' => 'required|string|max:100|unique:especialidades,nombre,' . $this->specialty_id,
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Activo,Inactivo', 
        ]);

        Especialidad::updateOrCreate(['id' => $this->specialty_id], [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado, 
        ]);

        session()->flash('message', 
            $this->specialty_id ? '¡Especialidad actualizada!' : '¡Especialidad creada!');

        $this->closeModal();
        $this->resetInputFields();
    }
    
    public function openModal() { $this->isOpen = true; }
    public function closeModal() { $this->isOpen = false; }
    private function resetInputFields()
    {
        $this->reset();
        $this->estado = null; 
    }
}