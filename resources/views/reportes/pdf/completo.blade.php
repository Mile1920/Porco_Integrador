<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Completo - {{ $stats['periodo']['inicio'] }} al {{ $stats['periodo']['fin'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #651c38; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #42172c; }
        .header p { margin: 5px 0; color: #694b54; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #42172c; border-bottom: 1px solid #e8d8cd; padding-bottom: 5px; }
        .stats { display: table; width: 100%; margin-bottom: 20px; }
        .stats div { display: table-cell; text-align: center; padding: 10px; background: #fff8f2; margin: 0 5px; }
        .stats strong { display: block; font-size: 18px; color: #42172c; }
        .stats span { color: #694b54; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #e8d8cd; padding: 6px; text-align: left; font-size: 12px; }
        th { background-color: #fff1e9; color: #42172c; font-weight: bold; }
        tr:nth-child(even) { background-color: #fff8f2; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #694b54; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Mina Porco - Reporte Completo del Sistema</h1>
        <p>Período: {{ \Carbon\Carbon::parse($stats['periodo']['inicio'])->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($stats['periodo']['fin'])->format('d/m/Y') }}</p>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Resumen Ejecutivo -->
    <div class="section">
        <h2>📊 Resumen Ejecutivo</h2>
        <div class="stats">
            <div>
                <strong>{{ $stats['trabajadores']['total'] }}</strong>
                <span>Trabajadores Activos</span>
            </div>
            <div>
                <strong>{{ $stats['ingresos']['ingresos'] }}</strong>
                <span>Ingresos</span>
            </div>
            <div>
                <strong>{{ $stats['incidentes']['total'] }}</strong>
                <span>Incidentes</span>
            </div>
            <div>
                <strong>{{ $stats['sensores']['activos'] }}</strong>
                <span>Sensores Activos</span>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="section">
        <h2>⏰ Actividad Reciente</h2>

        <h3>Últimos Ingresos</h3>
        <table>
            <thead>
                <tr>
                    <th>Trabajador</th>
                    <th>Tipo</th>
                    <th>Fecha/Hora</th>
                </tr>
            </thead>
            <tbody>
                @forelse($actividad['ingresos_recientes'] as $ingreso)
                <tr>
                    <td>{{ $ingreso->trabajador->nombre_completo ?? 'N/A' }}</td>
                    <td>{{ ucfirst($ingreso->tipo) }}</td>
                    <td>{{ \Carbon\Carbon::parse($ingreso->registrado_en)->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">No hay registros recientes</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <h3 style="margin-top: 20px;">Incidentes Recientes</h3>
        <table>
            <thead>
                <tr>
                    <th>Reportado por</th>
                    <th>Gravedad</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($actividad['incidentes_recientes'] as $incidente)
                <tr>
                    <td>{{ $incidente->trabajador->nombre_completo ?? 'Sistema' }}</td>
                    <td>{{ ucfirst($incidente->gravedad) }}</td>
                    <td>{{ $incidente->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">No hay incidentes recientes</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Estadísticas Detalladas -->
    <div class="section">
        <h2>📈 Estadísticas Detalladas</h2>

        <h4>Trabajadores</h4>
        <ul>
            <li><strong>Total Activos:</strong> {{ $stats['trabajadores']['total'] }}</li>
            <li><strong>Nuevos en el período:</strong> {{ $stats['trabajadores']['nuevos_mes'] }}</li>
        </ul>

        <h4>Control de Acceso</h4>
        <ul>
            <li><strong>Total Registros:</strong> {{ $stats['ingresos']['total'] }}</li>
            <li><strong>Ingresos:</strong> {{ $stats['ingresos']['ingresos'] }}</li>
            <li><strong>Salidas:</strong> {{ $stats['ingresos']['salidas'] }}</li>
        </ul>

        <h4>Seguridad</h4>
        <ul>
            <li><strong>Incidentes Totales:</strong> {{ $stats['incidentes']['total'] }}</li>
            <li><strong>Incidentes Críticos:</strong> {{ $stats['incidentes']['criticos'] }}</li>
            <li><strong>Incidentes Abiertos:</strong> {{ $stats['incidentes']['abiertos'] }}</li>
        </ul>

        <h4>Sensores IoT</h4>
        <ul>
            <li><strong>Sensores Activos:</strong> {{ $stats['sensores']['activos'] }}</li>
            <li><strong>Alertas Activas:</strong> {{ $stats['sensores']['alertas'] }}</li>
            <li><strong>Tipos de Sensores:</strong> {{ count($stats['sensores']['tipos']) }}</li>
        </ul>
    </div>

    <div class="footer">
        <p>Reporte generado por el Sistema de Gestión Mina Porco</p>
        <p>Confidencial - Solo para uso interno</p>
    </div>
</body>
</html>
