<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mis Reportes</title>
    <style>
        body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px}
        .header{display:flex;align-items:center;border-bottom:2px solid #651c38;padding-bottom:10px;margin-bottom:10px}
        .logo{width:80px;margin-right:10px}
        .company{font-weight:700}
        table{width:100%;border-collapse:collapse;margin-top:8px}
        th,td{border:1px solid #e8d8cd;padding:6px}
        th{background:#fff1e9;color:#42172c}
    </style>
</head>
<body>
    <div class="header">
        <div class="logo"><img src="{{ public_path('img/Logo.png') }}" style="max-width:100%"/></div>
        <div>
            <div class="company">Mina Porco S.A.</div>
            <div>Mis reportes — {{ now()->format('Y-m-d H:i') }}</div>
            @if(!empty($dateFrom) || !empty($dateTo))
                <div class="small">Periodo: {{ $dateFrom ?? '-' }} — {{ $dateTo ?? '-' }}</div>
            @endif
        </div>
    </div>

    @if(empty($only) || $only === 'ingresos')
        <h4>Ingresos / Salidas</h4>
        <table>
            <thead>
                <tr><th>Fecha</th><th>Tipo</th><th>Área</th><th>Subnivel</th></tr>
            </thead>
            <tbody>
                @foreach($rowsIngreso as $i)
                    <tr>
                        <td>{{ optional($i->registrado_en)->format('Y-m-d H:i') ?? $i->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ ucfirst($i->tipo) }}</td>
                        <td>{{ $i->area->nombre ?? '-' }}</td>
                        <td>{{ $i->area->nivel ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(empty($only) || $only === 'incidentes')
        <h4 style="margin-top:12px">Incidentes</h4>
        <table>
            <thead>
                <tr><th>Fecha</th><th>Área</th><th>Gravedad</th><th>Descripción</th></tr>
            </thead>
            <tbody>
                @foreach($rowsIncidente as $inc)
                    <tr>
                        <td>{{ $inc->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $inc->area->nombre ?? '-' }}</td>
                        <td>{{ ucfirst($inc->gravedad) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($inc->descripcion, 200) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div style="margin-top:12px;font-size:11px;color:#666">Documento generado por Mina Porco</div>
</body>
</html>
