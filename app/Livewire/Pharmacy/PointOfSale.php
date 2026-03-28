<?php

namespace App\Livewire\Pharmacy;

use App\Models\Lote;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\Venta;
use App\Models\DetalleVenta; // <-- AÑADIDO: Para consultar los más vendidos
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- AÑADIDO: Para la consulta
use Livewire\Component;

class PointOfSale extends Component
{
    // Búsqueda de productos
    public $search = '';
    public $searchResults = [];

    // Carrito de compras
    public $cart = [];
    public $total = 0;

    // Datos de la venta
    public $paciente_id;
    public $metodo_pago = 'Efectivo';

    // AÑADIDO: Nueva propiedad para guardar los más vendidos
    public $topSelling;

    /**
     * AÑADIDO: Función Mount
     * Esto se ejecuta UNA VEZ cuando el componente carga.
     * Busca los 5 medicamentos más vendidos que AÚN TENGAN STOCK.
     */
    public function mount()
    {
        // 1. Obtenemos los IDs de los 5 medicamentos más vendidos
        $topMedicamentoIds = DetalleVenta::select('medicamento_id', DB::raw('SUM(cantidad) as total_vendido'))
                                ->groupBy('medicamento_id')
                                ->orderBy('total_vendido', 'desc')
                                ->limit(5)
                                ->pluck('medicamento_id');

        // 2. Cargamos esos medicamentos, pero SOLO si todavía tienen lotes con stock
        $this->topSelling = Medicamento::with(['lotes' => function ($query) {
                                    $query->where('cantidad', '>', 0) // Solo lotes con stock
                                          ->orderBy('fecha_vencimiento', 'asc'); // Lotes más antiguos primero
                                }])
                                ->whereIn('id', $topMedicamentoIds)
                                ->whereHas('lotes', function ($query) { // Filtro: El medicamento debe tener al menos un lote con stock
                                    $query->where('cantidad', '>', 0);
                                })
                                ->get();
    }


    // BÚSQUEDA (Corregida para no mostrar lotes con stock 0)
    public function updatedSearch($value)
    {
        if (strlen($value) > 2) {
            $this->searchResults = Medicamento::with(['lotes' => function ($query) {
                $query->where('cantidad', '>', 0)->orderBy('fecha_vencimiento', 'asc');
            }])
            ->where(function($query) use ($value) {
                $query->where('nombre_comercial', 'like', '%' . $value . '%')
                      ->orWhere('principio_activo', 'like', '%' . $value . '%');
            })
            ->whereHas('lotes', function ($query) {
                $query->where('cantidad', '>', 0);
            })
            ->get();
        } else {
            // Si la búsqueda se limpia, reseteamos los resultados
            $this->searchResults = [];
        }
    }

    // Agrega un lote específico al carrito
    public function addToCart(Lote $lote)
    {
        if ($lote->cantidad <= 0) {
             $this->addError('cart', 'No se puede agregar un producto sin stock.');
             return;
        }

        if (isset($this->cart[$lote->id])) {
            if ($this->cart[$lote->id]['cantidad'] < $lote->cantidad) {
                $this->cart[$lote->id]['cantidad']++;
            }
        } else {
            $this->cart[$lote->id] = [
                'lote_id' => $lote->id,
                'medicamento_id' => $lote->medicamento->id,
                'nombre' => $lote->medicamento->nombre_comercial,
                'lote' => $lote->numero_lote,
                'precio' => $lote->precio_venta,
                'cantidad' => 1,
                'stock_disponible' => $lote->cantidad,
            ];
        }
        $this->calculateTotal();
        $this->resetErrorBag(); 
    }

    // Valida la cantidad en tiempo real
    public function updateCartQuantity($loteId, $cantidad)
    {
        $cantidad = (int)$cantidad;
        if (isset($this->cart[$loteId])) {
            
            $stock = $this->cart[$loteId]['stock_disponible'];
            $this->cart[$loteId]['cantidad'] = $cantidad;

            $this->resetErrorBag('cart.'.$loteId.'.stock');
            $this->resetErrorBag('cart.'.$loteId.'.cantidad');

            if ($cantidad > $stock) {
                $this->addError('cart.'.$loteId.'.stock', 'Stock máx: ' . $stock);
            } 
            
            if ($cantidad < 1) {
                $this->addError('cart.'.$loteId.'.cantidad', 'Debe ser al menos 1.');
            }

            $this->calculateTotal();
        }
    }

    // Elimina un item del carrito
    public function removeFromCart($loteId)
    {
        unset($this->cart[$loteId]);
        $this->calculateTotal();
    }

    // Calcula el total del carrito
    public function calculateTotal()
    {
        $this->total = collect($this->cart)->sum(function ($item) {
            return $item['precio'] * $item['cantidad'];
        });
    }
    
    // Procesa y guarda la venta
    public function processSale()
    {
        $this->validate([
            'metodo_pago' => 'required',
            'cart' => 'required|array|min:1'
        ], [
            'cart.min' => 'El carrito no puede estar vacío.'
        ]);

        $hasError = false;
        foreach ($this->cart as $loteId => $item) {
            if ($item['cantidad'] < 1) {
                $this->addError('cart.'.$loteId.'.cantidad', 'La cantidad debe ser al menos 1.');
                $hasError = true;
            }
            if ($item['cantidad'] > $item['stock_disponible']) {
                $this->addError('cart.'.$loteId.'.stock', 'No puedes vender más del stock (' . $item['stock_disponible'] . ').');
                $hasError = true;
            }
        }

        if ($hasError) {
            return;
        }
        
        DB::transaction(function () {
            $venta = Venta::create([
                'user_id' => Auth::id(),
                'paciente_id' => $this->paciente_id,
                'monto_total' => $this->total,
                'metodo_pago' => $this->metodo_pago,
                'estado' => 'Completada',
            ]);

            foreach ($this->cart as $item) {
                $venta->detalles()->create([
                    'medicamento_id' => $item['medicamento_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario_en_venta' => $item['precio'],
                    'subtotal' => $item['precio'] * $item['cantidad'],
                ]);
                
                $lote = Lote::find($item['lote_id']);
                if ($lote) { 
                    $lote->decrement('cantidad', $item['cantidad']);
                }
            }
        });

        session()->flash('message', '¡Venta registrada exitosamente!');
        $this->resetAll();
    }

    // Resetea todo el componente
    public function resetAll()
    {
        $this->reset(['search', 'searchResults', 'cart', 'total', 'paciente_id', 'metodo_pago']);
        $this->metodo_pago = 'Efectivo';
        $this->resetErrorBag(); 
        
        // AÑADIDO: Recargamos los más vendidos después de una venta
        $this->mount();
    }

    public function render()
    {
        $pacientes = Paciente::orderBy('nombre_completo')->get();
        return view('livewire.pharmacy.point-of-sale', compact('pacientes'))
            ->layout('layouts.app');
    }
}

