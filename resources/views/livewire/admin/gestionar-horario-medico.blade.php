<div class="mt-4">

    @if (session()->has('horario_message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('horario_message') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="save">
        <h4 class="text-lg font-semibold text-gray-800">{{ $formTitle }}</h4>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4 p-4 border rounded-lg bg-gray-50">
            
            <div>
                <label for="dia_semana" class="block text-sm font-medium text-gray-700">Día</label>
                <select wire:model.defer="dia_semana" id="dia_semana" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccione un día</option>
                    @foreach ($dias as $numero => $nombre)
                        <option value="{{ $numero }}">{{ $nombre }}</option>
                    @endforeach
                </select>
                @error('dia_semana') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="hora_inicio_hr" class="block text-sm font-medium text-gray-700">Hora Inicio</label>
                <div class="flex items-center gap-x-1 mt-1">
                    <input type="number" wire:model.defer="hora_inicio_hr" id="hora_inicio_hr" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" min="1" max="12" placeholder="HH">
                    <span class="text-gray-500">:</span>
                    <input type="number" wire:model.defer="hora_inicio_min" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" min="0" max="59" placeholder="MM">
                    <select wire:model.defer="hora_inicio_ampm" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                    </select>
                </div>
                @error('hora_inicio_hr') <span class="text-red-500 text-xs d-block">{{ $message }}</span> @enderror
                @error('hora_inicio_min') <span class="text-red-500 text-xs d-block">{{ $message }}</span> @enderror
                @error('hora_inicio') <span class="text-red-500 text-xs d-block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="hora_fin_hr" class="block text-sm font-medium text-gray-700">Hora Fin</label>
                <div class="flex items-center gap-x-1 mt-1">
                    <input type="number" wire:model.defer="hora_fin_hr" id="hora_fin_hr" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" min="1" max="12" placeholder="HH">
                    <span class="text-gray-500">:</span>
                    <input type="number" wire:model.defer="hora_fin_min" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" min="0" max="59" placeholder="MM">
                    <select wire:model.defer="hora_fin_ampm" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                    </select>
                </div>
                @error('hora_fin_hr') <span class="text-red-500 text-xs d-block">{{ $message }}</span> @enderror
                @error('hora_fin_min') <span class="text-red-500 text-xs d-block">{{ $message }}</span> @enderror
                @error('hora_fin') <span class="text-red-500 text-xs d-block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-end gap-x-2">
                @if ($editingHorarioId)
                    <button type="button" wire:click="cancelEdit" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-300 disabled:opacity-25 transition w-full">
                        Cancelar
                    </button>
                @endif
                
                <button type="submit" 
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest disabled:opacity-25 transition w-full
                               {{ $editingHorarioId ? 'bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 focus:border-indigo-700 focus:ring-indigo-300' : 'bg-blue-600 hover:bg-blue-500 active:bg-blue-700 focus:border-blue-700 focus:ring-blue-300' }}">
                    <span wire:loading.remove wire:target="save">
                        {{ $editingHorarioId ? 'Actualizar' : 'Añadir' }}
                    </span>
                    <span wire:loading wire:target="save">Guardando...</span>
                </button>
            </div>
        </div>
    </form>

    <h4 class="text-lg font-semibold text-gray-800 mt-8">Horarios Registrados</h4>
    <div class="mt-4 flow-root">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse ($horarios as $horario)
                <li class="flex items-center justify-between py-3">
                    <div class="flex-1">
                        <p class="text-md font-medium text-gray-900">
                            {{ $dias[$horario->dia_semana] ?? 'Día no válido' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            de {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('h:i A') }} 
                            a {{ \Carbon\Carbon::parse($horario->hora_fin)->format('h:i A') }}
                        </p>
                    </div>
                    
                    <div class="flex gap-x-4">
                        <button 
                            wire:click="editHorario({{ $horario->id }})"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            Editar
                        </button>
                        <button 
                            wire:click="deleteHorario({{ $horario->id }})"
                            wire:confirm="¿Estás seguro de eliminar este horario?"
                            class="text-red-500 hover:text-red-700 text-sm font-medium">
                            Eliminar
                        </button>
                    </div>
                </li>
            @empty
                <li class="py-3">
                    <p class="text-center text-gray-500">No hay horarios registrados para este médico.</p>
                </li>
            @endforelse
        </ul>
    </div>

</div>