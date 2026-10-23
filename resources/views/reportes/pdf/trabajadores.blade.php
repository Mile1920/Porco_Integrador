<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Trabajadores - {{ now()->format('d/m/Y') }}</title>
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
        <h1>Mina Porco - Reporte de Trabajadores</h1>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="stats">
        <div>
            <strong>{{ $stats['total'] }}</strong>
            <span>Total Registrados</span>
        </div>
        <div>
            <strong>{{ $stats['activos'] }}</strong>
            <span>Activos</span>
        </div>
        <div>
            <strong>{{ $stats['inactivos'] }}</strong>
            <span>Inactivos</span>
        </div>
        <div>
            <strong>{{ $stats['con_usuario'] }}</strong>
            <span>Con Acceso Sistema</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>Cargo</th>
                <th>Área</th>
                <th>Estado</th>
                <th>Acceso Sistema</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trabajadores as $trabajador)
            <tr>
                <td>{{ $trabajador->nombre_completo }}</td>
                <td>{{ $trabajador->cargo->nombre ?? '-' }}</td>
                <td>{{ $trabajador->area->nombre ?? '-' }}</td>
                <td>{{ $trabajador->activo ? 'Activo' : 'Inactivo' }}</td>
                <td>{{ $trabajador->user ? 'Sí' : 'No' }}</td>
                <td>{{ $trabajador->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No hay trabajadores registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado por el Sistema de Gestión Mina Porco</p>
    </div>
</body>
</html>
