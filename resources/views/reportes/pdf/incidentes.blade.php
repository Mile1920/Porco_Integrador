<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Incidentes - {{ now()->format('d/m/Y') }}</title>
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
        <h1>Mina Porco - Reporte de Incidentes</h1>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="stats">
        <div>
            <strong>{{ $stats['total'] }}</strong>
            <span>Total Incidentes</span>
        </div>
        <div>
            <strong>{{ $stats['abiertos'] }}</strong>
            <span>Abiertos</span>
        </div>
        <div>
            <strong>{{ $stats['cerrados'] }}</strong>
            <span>Resueltos</span>
        </div>
        <div>
            <strong>{{ $stats['criticos'] }}</strong>
            <span>Críticos</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Reportado por</th>
                <th>Descripción</th>
                <th>Gravedad</th>
                <th>Estado</th>
                <th>Fecha Reporte</th>
                <th>Fecha Cierre</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incidentes as $incidente)
            <tr>
                <td>{{ $incidente->trabajador->nombre_completo ?? 'Sistema' }}</td>
                <td>{{ Str::limit($incidente->descripcion, 50) }}</td>
                <td>{{ ucfirst($incidente->gravedad) }}</td>
                <td>{{ ucfirst($incidente->estado) }}</td>
                <td>{{ $incidente->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $incidente->updated_at != $incidente->created_at ? $incidente->updated_at->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No hay incidentes registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado por el Sistema de Gestión Mina Porco</p>
    </div>
</body>
</html>
