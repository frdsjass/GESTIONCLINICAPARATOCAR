<?php

namespace App\Livewire\Pharmacy;

use App\Models\Medicamento;
use App\Models\Lote;
use Illuminate\Validation\Rule; // <-- AÑADIDO: Importamos la clase Rule
use Livewire\Component;
use Livewire\WithPagination;

class Inventory extends Component
{
    use WithPagination;

    public $medicamento_id, $nombre_comercial, $principio_activo, $laboratorio, $presentacion, $requiere_receta = false;
    public $lote_id, $selected_medicamento_id, $numero_lote, $fecha_vencimiento, $cantidad, $precio_venta, $stock_minimo;
    public $search = '';
    public $isMedicamentoModalOpen = false;
    public $isLoteModalOpen = false;

    public function render()
    {
        $medicamentos = Medicamento::with('lotes')
            ->where(function($query) {
                $query->where('nombre_comercial', 'like', '%' . $this->search . '%')
                      ->orWhere('principio_activo', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.pharmacy.inventory', compact('medicamentos'))
            ->layout('layouts.app');
    }

    // --- (Métodos createMedicamento, editMedicamento, storeMedicamento, deleteMedicamento sin cambios) ---
    // ... (Tu código de medicamentos sin cambios va aquí) ...

    public function createMedicamento()
    {
        $this->resetMedicamentoInputFields();
        $this->isMedicamentoModalOpen = true;
    }

    public function editMedicamento($id)
    {
        $medicamento = Medicamento::findOrFail($id);
        $this->medicamento_id = $id;
        $this->nombre_comercial = $medicamento->nombre_comercial;
        $this->principio_activo = $medicamento->principio_activo;
        $this->laboratorio = $medicamento->laboratorio;
        $this->presentacion = $medicamento->presentacion;
        $this->requiere_receta = $medicamento->requiere_receta;
        $this->isMedicamentoModalOpen = true;
    }

    public function storeMedicamento()
    {
        $this->validate([
            'nombre_comercial' => 'required|string|max:255',
            'principio_activo' => 'required|string|max:255',
            'requiere_receta' => 'boolean',
        ]);

        Medicamento::updateOrCreate(['id' => $this->medicamento_id], [
            'nombre_comercial' => $this->nombre_comercial,
            'principio_activo' => $this->principio_activo,
            'laboratorio' => $this->laboratorio,
            'presentacion' => $this->presentacion,
            'requiere_receta' => $this->requiere_receta,
        ]);

        session()->flash('message', $this->medicamento_id ? '¡Producto actualizado!' : '¡Producto agregado al catálogo!');
        $this->closeMedicamentoModal();
    }

    public function deleteMedicamento($id)
    {
        Medicamento::find($id)->delete();
        session()->flash('message', '¡Producto y todos sus lotes eliminados!');
    }


    // --- (Métodos de Lote) ---

    public function createLote($medicamentoId)
    {
        $this->resetLoteInputFields();
        $this->selected_medicamento_id = $medicamentoId;
        $this->isLoteModalOpen = true;
    }

    public function storeLote()
    {
        // VALIDACIÓN ACTUALIZADA
        $this->validate([
            'numero_lote' => [ // <-- CAMBIADO: Convertido a array
                'required',
                'string',
                'max:255',
                // REGLA AÑADIDA: El lote debe ser único en la tabla 'lotes',
                // pero solo donde el 'medicamento_id' sea el que estamos guardando.
                Rule::unique('lotes')->where(function ($query) {
                    return $query->where('medicamento_id', $this->selected_medicamento_id);
                })
            ],
            'fecha_vencimiento' => 'required|date',
            'cantidad' => 'required|integer|min:1', // <-- CAMBIADO: min:0 a min:1 (no tiene sentido agregar lotes sin stock)
            'precio_venta' => 'required|numeric|min:0.01', // <-- CAMBIADO: min:0 a min:0.01 (el precio no debe ser 0)
            'stock_minimo' => 'required|integer|min:0', // min:0 está bien aquí
        ], [
            // Mensaje de error personalizado para la regla 'unique'
            'numero_lote.unique' => 'Este número de lote ya existe para este medicamento.'
        ]);
        // FIN DE LA VALIDACIÓN

        Lote::create([
            'medicamento_id' => $this->selected_medicamento_id,
            'numero_lote' => $this->numero_lote,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'cantidad' => $this->cantidad,
            'precio_venta' => $this->precio_venta,
            'stock_minimo' => $this->stock_minimo,
        ]);

        session()->flash('message', '¡Lote agregado al inventario exitosamente!');
        $this->closeLoteModal();
    }

    public function deleteLote($id)
    {
        Lote::find($id)->delete();
        session()->flash('message', '¡Lote eliminado del inventario!');
    }

    public function closeMedicamentoModal() { $this->isMedicamentoModalOpen = false; }
    public function closeLoteModal() { $this->isLoteModalOpen = false; }

    private function resetMedicamentoInputFields()
    {
        $this->reset(['medicamento_id', 'nombre_comercial', 'principio_activo', 'laboratorio', 'presentacion', 'requiere_receta']);
        $this->requiere_receta = false;
    }

    private function resetLoteInputFields()
    {
        $this->reset(['lote_id', 'numero_lote', 'fecha_vencimiento', 'cantidad', 'precio_venta', 'stock_minimo']);
    }
}
