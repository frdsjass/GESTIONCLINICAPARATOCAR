<?php

namespace App\Livewire\Admin;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role; 

class PatientsManager extends Component
{
    use WithPagination;

    public $nombre_completo, $carnet_identidad, $fecha_nacimiento, $genero;
    public $telefono, $direccion, $antecedentes_medicos, $alergias;
    public $estado;

    public $email, $password;

    public $paciente_id, $user_id;
    public $isOpen = false;
    public $search = '';
    
    public $showInactive = false;

    protected $listeners = ['render' => 'render'];

    public function render()
    {
        $pacientesQuery = Paciente::with('user')
            ->where(function($query) {
                $query->where('nombre_completo', 'like', '%' . $this->search . '%')
                    ->orWhere('carnet_identidad', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($subQuery) {
                        if (!empty($this->search)) {
                            $subQuery->where('email', 'like', '%' . $this->search . '%');
                        }
                    });
            });

        $pacientesQuery->when(!$this->showInactive, function ($query) {
            return $query->where('estado', 'Activo');
        });

        $pacientes = $pacientesQuery->latest()->paginate(10);

        return view('livewire.admin.patients-manager', compact('pacientes'))
            ->layout('layouts.app');
    }

    public function toggleEstado($id)
    {
        $paciente = Paciente::find($id);
        if ($paciente) {
            $nuevoEstado = ($paciente->estado == 'Activo') ? 'Inactivo' : 'Activo';
            
            $paciente->estado = $nuevoEstado;
            $paciente->save();
            
            session()->flash('message', 'Estado del paciente actualizado a ' . $nuevoEstado . '.');
            $this->dispatch('render');
        }
    }
    
    public function viewHistory($id)
    {
        if (route()->has('admin.pacientes.historial')) {
            return redirect()->route('admin.pacientes.historial', $id);
        } else {
             session()->flash('error', 'La ruta para ver el historial no está definida.');
             return back();
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function edit($id)
    {
        $paciente = Paciente::with('user')->findOrFail($id);
        $this->paciente_id = $id;
        $this->user_id = $paciente->user_id;

        $this->nombre_completo = $paciente->nombre_completo;
        $this->carnet_identidad = $paciente->carnet_identidad;
        $this->fecha_nacimiento = $paciente->fecha_nacimiento;
        $this->genero = $paciente->genero;
        $this->telefono = $paciente->telefono;
        $this->direccion = $paciente->direccion;
        $this->antecedentes_medicos = $paciente->antecedentes_medicos;
        $this->alergias = $paciente->alergias;
        $this->estado = $paciente->estado;

        $this->email = $paciente->user->email ?? null;
        $this->password = null;

        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'nombre_completo' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                 Rule::unique('users', 'email')->ignore($this->user_id)->whereNotNull('email'),
            ],
            'password' => [
                Rule::requiredIf(fn() => !$this->user_id && !empty($this->email)),
                'nullable', 
                'min:8',
            ],

            'carnet_identidad' => [
                'required', 'string', 'max:20',
                 Rule::unique('pacientes', 'carnet_identidad')->ignore($this->paciente_id)
            ],
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:Masculino,Femenino,Otro',
            'telefono' => 'required|string|max:25',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        DB::transaction(function () {
            $userIdToLink = $this->user_id;

            if (!empty($this->email)) {
                $userData = [
                    'name' => $this->nombre_completo,
                    'email' => $this->email,
                ];
                if (!empty($this->password)) {
                    $userData['password'] = Hash::make($this->password);
                }

                $user = User::updateOrCreate(['id' => $this->user_id], $userData);
                $userIdToLink = $user->id;

                if (!$this->user_id) {
                    if (Role::where('name', 'Paciente')->exists()) {
                        $user->assignRole('Paciente');
                    } else {
                        throw new \Exception('El rol Paciente no existe.');
                    }
                }
            } else {
                if ($this->user_id) {
                     User::find($this->user_id)->delete();
                     $userIdToLink = null;
                 }
            }

            Paciente::updateOrCreate(['id' => $this->paciente_id], [
                'user_id' => $userIdToLink,
                'nombre_completo' => $this->nombre_completo,
                'carnet_identidad' => $this->carnet_identidad,
                'fecha_nacimiento' => $this->fecha_nacimiento,
                'genero' => $this->genero,
                'telefono' => $this->telefono,
                'direccion' => $this->direccion,
                'antecedentes_medicos' => $this->antecedentes_medicos,
                'alergias' => $this->alergias,
                'estado' => $this->estado,
            ]);
        });

        session()->flash('message',
            $this->paciente_id ? '¡Paciente actualizado!' : '¡Paciente registrado!');

        $this->closeModal();
        $this->resetInputFields();
    }
    
    public function delete($id)
    {
        $paciente = Paciente::find($id);
        if ($paciente) {
            DB::transaction(function () use ($paciente) {
                if ($paciente->user_id) {
                    $paciente->user->delete();
                }
                $paciente->delete();
            });
            session()->flash('message', '¡Paciente eliminado permanentemente!');
        }
    }


    public function openModal() { $this->isOpen = true; }
    public function closeModal() { $this->isOpen = false; }

    private function resetInputFields()
    {
        $this->reset([
            'nombre_completo', 'carnet_identidad', 'fecha_nacimiento', 'genero',
            'telefono', 'direccion', 'antecedentes_medicos', 'alergias', 'estado',
            'email', 'password',
            'paciente_id', 'user_id'
        ]);
        $this->estado = null;
    }
}