<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas Mina Porco</title>
    <style>
        @page { margin: 20mm 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #333; }
        h1 { font-size: 18pt; color: #c52d59; margin-bottom: 5px; }
        h2 { font-size: 13pt; color: #651c38; margin-top: 20px; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        h3 { font-size: 11pt; color: #555; margin: 12px 0 6px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { width: 70px; }
        .header h1 { margin: 5px 0; }
        .header p { font-size: 9pt; color: #777; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        th, td { padding: 5px 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #c52d59; color: #fff; font-size: 9pt; }
        td { font-size: 9pt; }
        .totals { display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0; }
        .totals .card { flex: 1; min-width: 100px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; text-align: center; }
        .totals .card .val { font-size: 16pt; font-weight: bold; color: #c52d59; }
        .totals .card .lbl { font-size: 7pt; color: #888; text-transform: uppercase; }
        .footer { text-align: center; margin-top: 30px; font-size: 8pt; color: #aaa; border-top: 1px solid #eee; padding-top: 8px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Centro Minero Porco</h1>
        <p>Reporte de Estadísticas — Generado {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <h2>Resumen General</h2>
    <div class="totals">
        <div class="card"><div class="val">{{ $totalTrabajadores }}</div><div class="lbl">Trabajadores</div></div>
        <div class="card"><div class="val">{{ $totalAreas }}</div><div class="lbl">Áreas</div></div>
        <div class="card"><div class="val">{{ $totalCargos }}</div><div class="lbl">Cargos</div></div>
        <div class="card"><div class="val">{{ $ingresosHoy }}</div><div class="lbl">Ingresos Hoy</div></div>
        <div class="card"><div class="val">{{ $salidasHoy }}</div><div class="lbl">Salidas Hoy</div></div>
    </div>

    <table>
        <tr><th>Métrica</th><th>Valor</th></tr>
        <tr><td>Ingresos este mes</td><td>{{ $ingresosMes }}</td></tr>
        <tr><td>Salidas este mes</td><td>{{ $salidasMes }}</td></tr>
        <tr><td>Incidentes pendientes</td><td>{{ $incidentesPendientes }}</td></tr>
        <tr><td>Incidentes completados</td><td>{{ $incidentesCerrados }}</td></tr>
        <tr><td>Incidentes críticos</td><td>{{ $incidentesCriticos }}</td></tr>
    </table>

    <h2>Trabajadores por Turno</h2>
    <table>
        <tr><th>Turno</th><th>Cantidad</th></tr>
        @foreach($turnos as $turno => $count)
        <tr><td>{{ ucfirst($turno) }}</td><td>{{ $count }}</td></tr>
        @endforeach
    </table>

    <h2>Trabajadores por Área</h2>
    <table>
        <tr><th>Área</th><th>Trabajadores</th></tr>
        @foreach($trabajadoresPorArea as $area)
        <tr><td>{{ $area['label'] }}</td><td>{{ $area['value'] }}</td></tr>
        @endforeach
    </table>

    <div class="page-break"></div>

    <h2>Ingresos/Salidas — Últimos 7 Días</h2>
    <table>
        <tr><th>Fecha</th><th>Ingresos</th><th>Salidas</th></tr>
        @foreach($labels7d as $i => $label)
        <tr>
            <td>{{ $label }}</td>
            <td>{{ $ingresos7d[$i] ?? 0 }}</td>
            <td>{{ $salidas7d[$i] ?? 0 }}</td>
        </tr>
        @endforeach
    </table>

    <h2>Ingresos Hoy por Hora</h2>
    <table>
        <tr><th>Hora</th><th>Ingresos</th></tr>
        @foreach($horasPico as $hora => $count)
        <tr><td>{{ sprintf('%02d:00', $hora) }}</td><td>{{ $count }}</td></tr>
        @endforeach
    </table>

    <h2>Incidentes por Gravedad</h2>
    <table>
        <tr><th>Gravedad</th><th>Cantidad</th></tr>
        @foreach($incidentesPorGravedad as $g => $count)
        <tr><td>{{ ucfirst($g) }}</td><td>{{ $count }}</td></tr>
        @endforeach
    </table>

    <h2>Incidentes — Últimos 6 Meses</h2>
    <table>
        <tr><th>Mes</th><th>Incidentes</th></tr>
        @foreach($labels6m as $i => $label)
        <tr><td>{{ $label }}</td><td>{{ $incidentes6m[$i] ?? 0 }}</td></tr>
        @endforeach
    </table>

    <div class="footer">
        Centro Minero Porco — Sistema de Gestión Integral — {{ date('Y') }}
    </div>
</body>
</html>
