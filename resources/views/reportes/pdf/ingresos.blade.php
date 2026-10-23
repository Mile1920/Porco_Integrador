<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ingresos - {{ $stats['periodo']['inicio'] }} al {{ $stats['periodo']['fin'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #651c38; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #42172c; }
        .header p { margin: 5px 0; color: #694b54; }
        .stats { display: table; width: 100%; margin-bottom: 20px; }
        .stats div { display: table-cell; text-align: center; padding: 10px; background: #fff8f2; margin: 0 5px; }
        .stats strong { display: block; font-size: 18px; color: #42172c; }
        .stats span { color: #694b54; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e8d8cd; padding: 8px; text-align: left; }
        th { background-color: #fff1e9; color: #42172c; font-weight: bold; }
        tr:nth-child(even) { background-color: #fff8f2; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #694b54; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Mina Porco - Reporte de Ingresos y Salidas</h1>
        <p>Período: {{ \Carbon\Carbon::parse($stats['periodo']['inicio'])->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($stats['periodo']['fin'])->format('d/m/Y') }}</p>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="stats">
        <div>
            <strong>{{ $stats['total_registros'] }}</strong>
            <span>Total Registros</span>
        </div>
        <div>
            <strong>{{ $stats['ingresos'] }}</strong>
            <span>Ingresos</span>
        </div>
        <div>
            <strong>{{ $stats['salidas'] }}</strong>
            <span>Salidas</span>
        </div>
        <div>
            <strong>{{ $stats['trabajadores_unicos'] }}</strong>
            <span>Trabajadores Únicos</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Trabajador</th>
                <th>Área</th>
                <th>Tipo</th>
                <th>Fecha/Hora</th>
                <th>Subnivel</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ingresos as $ingreso)
            <tr>
                <td>{{ $ingreso->trabajador->nombre_completo ?? 'N/A' }}</td>
                <td>{{ $ingreso->area->nombre ?? $ingreso->area_id }}</td>
                <td>{{ ucfirst($ingreso->tipo) }}</td>
                <td>{{ \Carbon\Carbon::parse($ingreso->registrado_en)->format('d/m/Y H:i') }}</td>
                <td>{{ $ingreso->area->nivel ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No hay registros de ingresos en el período seleccionado</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado por el Sistema de Gestión Mina Porco</p>
    </div>
</body>
</html>
