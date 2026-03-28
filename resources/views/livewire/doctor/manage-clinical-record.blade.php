<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-medium text-gray-900">
                    Ficha Clínica de la Consulta
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Cita del {{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->format('d/m/Y \a \l\a\s H:i A') }}
                </p>
            </div>
            <a href="{{ route('doctor.dashboard') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                &larr; Volver al Panel
            </a>
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h3 class="text-lg font-bold border-b pb-2 mb-4">Datos del Paciente</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <strong class="text-gray-600">Nombre:</strong>
                    <p>{{ $cita->paciente->nombre_completo }}</p>
                </div>
                <div>
                    <strong class="text-gray-600">Carnet de Identidad:</strong>
                    <p>{{ $cita->paciente->carnet_identidad }}</p>
                </div>
                <div>
                    <strong class="text-gray-600">Fecha de Nacimiento:</strong>
                    <p>{{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->format('d/m/Y') }} ({{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }} años)</p>
                </div>
                <div>
                    <strong class="text-gray-600">Alergias Conocidas:</strong>
                    <p>{{ $cita->paciente->alergias ?: 'Ninguna registrada' }}</p>
                </div>
                <div class="col-span-2">
                    <strong class="text-gray-6