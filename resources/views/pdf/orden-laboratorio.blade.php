<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Laboratorio</title>
    <!-- Estilos CSS simples para el PDF -->
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 95%; /* Un poco más ancho para la nueva tabla */
            margin: 20px auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #000;
        }
        .header p {
            margin: 0;
            font-size: 12px;
            color: #555;
        }
        .info-paciente, .info-orden {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-paciente th, .info-paciente td,
        .info-orden th, .info-orden td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .info-paciente th, .info-orden th {
            background-color: #f9f9f9;
            width: 25%;
            font-weight: bold;
        }
        .resultados-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .resultados-tabla th, .resultados-tabla td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            /* ¡NUEVO! Evita que el texto se desborde */
            word-wrap: break-word;
        }
        .resultados-tabla thead {
            background-color: #337ab7;
            color: white;
        }
        .resultados-tabla th {
            font-size: 12px;
            text-transform: uppercase;
        }
        /* ¡NUEVO! Ajuste de anchos de columna */
        .col-examen { width: 25%; }
        .col-resultado { width: 25%; }
        .col-referencia { width: 25%; }
        .col-metodo { width: 25%; }
        
        .conclusion {
            border: 1px solid #ddd;
            padding: 12px;
            background-color: #fdfdfd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .conclusion h3 {
            margin-top: 0;
            color: #337ab7;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #888;
            margin-top: 30px;
            width: 100%;
            position: fixed; /* Fija el footer al final de la página */
            bottom: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Clínica Más Cerca del Cielo</h1>
        <p>Resultados de Exámenes de Laboratorio</p>
    </div>

    <div class="container">
        
        <h3>Información del Paciente</h3>
        <table class="info-paciente">
            <tr>
                <th>Paciente:</th>
                <td>{{ $orden->paciente->nombre_completo ?? 'N/A' }}</td>
                <th>C.I.:</th>
                <td>{{ $orden->paciente->carnet_identidad ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Edad:</th>
                <td>{{ $orden->paciente->fecha_nacimiento ? $orden->paciente->fecha_nacimiento->age : 'N/A' }} años</td>
                <th>Género:</th>
                <td>{{ $orden->paciente->genero ?? 'N/A' }}</td>
            </tr>
        </table>

        <h3>Información de la Orden</h3>
        <table class="info-orden">
            <tr>
                <th>Orden #:</th>
                <td>{{ $orden->id }}</td>
                <th>Fecha de Emisión:</th>
                <td>{{ $orden->created_at->format('d/m/Y H:i A') }}</td>
            </tr>
            <tr>
                <th>Médico Solicitante:</th>
                <td colspan="3">Dr(a). {{ $orden->medico->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Notas del Médico:</th>
                <td colspan="3">{{ $orden->notas_medico ?? 'Ninguna' }}</td>
            </tr>
        </table>


        <h3>Resultados de Exámenes</h3>
        <table class="resultados-tabla">
            <thead>
                <tr>
                    <!-- ¡NUEVAS COLUMNAS! -->
                    <th class="col-examen">Examen Solicitado</th>
                    <th class="col-resultado">Resultado</th>
                    <th class="col-referencia">Valores de Referencia</th>
                    <th class="col-metodo">Metodología</th>
                </tr>
            </thead>
            <tbody>
                <!-- ¡NUEVOS CAMPOS EN LA FILA! -->
                @foreach($orden->detalles as $detalle)
                <tr>
                    <td><strong>{{ $detalle->tipoExamen->nombre ?? 'Examen no especificado' }}</strong></td>
                    <td>{{ $detalle->resultados ?? 'N/A' }}</td>
                    <td>{{ $detalle->valores_referencia ?? 'N/A' }}</td>
                    <td>{{ $detalle->metodologia ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @if($conclusion)
        <div class="conclusion">
            <h3>Conclusión General / Resumen</h3>
            <p>{{ $conclusion }}</p>
        </div>
        @endif

    </div>

    <div class="footer">
        Este es un documento generado automáticamente por el Sistema de Gestión Clínica.
        <br>
        Fecha de Impresión: {{ now()->format('d/m/Y H:i A') }}
    </div>

</body>
</html>