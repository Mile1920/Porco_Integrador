@extends('layouts.app')
@section('title', 'Estadísticas del Sistema')
@section('content')

<style>
.chart-card { min-height: 400px; }
.chart-card .card-body { position: relative; padding: 15px; }
.chart-container { position: relative; height: 300px; width: 100%; }
.chart-fallback { display: none; text-align: center; padding: 40px 20px; color: #999; }
.stat-value { font-size: 2rem; font-weight: 700; line-height: 1.2; }
.stat-label { font-size: 0.85rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.05em; }
.mini-card { padding: 1.25rem; border-radius: 12px; transition: transform 0.2s; }
.mini-card:hover { transform: translateY(-3px); }
.mini-card .icon-circle { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
</style>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title mb-0"><i class="material-icons" style="vertical-align: middle;">bar_chart</i> Estadísticas del Sistema</h4>
                    <p class="card-category mb-0">Datos agregados y tendencias — {{ now()->format('d/m/Y H:i') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('estadisticas.pdf') }}" class="btn btn-outline-light btn-sm">
                        <i class="material-icons" style="vertical-align: middle; font-size: 1rem;">picture_as_pdf</i> PDF
                    </a>
                    <button class="btn btn-outline-light btn-sm" onclick="location.reload()">
                        <i class="material-icons" style="vertical-align: middle; font-size: 1rem;">refresh</i> Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtro por fechas -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Desde</label>
                        <input type="date" name="desde" class="form-control" value="{{ request('desde', today()->subDays(30)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="hasta" class="form-control" value="{{ request('hasta', today()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary form-control">
                            <i class="material-icons" style="vertical-align: middle; font-size: 1rem;">filter_list</i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('estadisticas.index') }}" class="btn btn-secondary form-control">
                            <i class="material-icons" style="vertical-align: middle; font-size: 1rem;">clear</i> Limpiar
                        </a>
                    </div>
                    <div class="col-md-2 text-end">
                        <small class="text-muted">
                            Período: {{ request('desde', today()->subDays(30)->format('d/m/Y')) }} — {{ request('hasta', today()->format('d/m/Y')) }}
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="mini-card" style="background: linear-gradient(135deg, var(--porco-wine), var(--porco-raspberry)); color: #fff;">
            <div class="d-flex align-items-center">
                <div class="icon-circle me-3" style="background: rgba(255,255,255,0.2);">
                    <i class="material-icons">people</i>
                </div>
                <div>
                    <div class="stat-value">{{ $totalTrabajadores }}</div>
                    <div class="stat-label">Trabajadores Activos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="mini-card" style="background: linear-gradient(135deg, var(--porco-wine), var(--porco-raspberry)); color: #fff;">
            <div class="d-flex align-items-center">
                <div class="icon-circle me-3" style="background: rgba(255,255,255,0.2);">
                    <i class="material-icons">login</i>
                </div>
                <div>
                    <div class="stat-value">{{ $ingresosHoy }}</div>
                    <div class="stat-label">Ingresos Hoy</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="mini-card" style="background: linear-gradient(135deg, var(--porco-wine), var(--porco-raspberry)); color: #fff;">
            <div class="d-flex align-items-center">
                <div class="icon-circle me-3" style="background: rgba(255,255,255,0.2);">
                    <i class="material-icons">logout</i>
                </div>
                <div>
                    <div class="stat-value">{{ $salidasHoy }}</div>
                    <div class="stat-label">Salidas Hoy</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="mini-card" style="background: linear-gradient(135deg, var(--porco-wine), var(--porco-raspberry)); color: #fff;">
            <div class="d-flex align-items-center">
                <div class="icon-circle me-3" style="background: rgba(255,255,255,0.2);">
                    <i class="material-icons">warning</i>
                </div>
                <div>
                    <div class="stat-value">{{ $incidentesAbiertos }}</div>
                    <div class="stat-label">Incidentes Abiertos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="mini-card" style="background: linear-gradient(135deg, var(--porco-wine), var(--porco-raspberry)); color: #fff;">
            <div class="d-flex align-items-center">
                <div class="icon-circle me-3" style="background: rgba(255,255,255,0.2);">
                    <i class="material-icons">notifications_active</i>
                </div>
                <div>
                    <div class="stat-value">{{ $alertasPeriodo }}</div>
                    <div class="stat-label">Alertas Generadas</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de datos del período -->
@if(isset($dataTable) && count($dataTable) > 0)
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="material-icons" style="vertical-align: middle;">table_chart</i> Todos los registros del período</h5>
                <div class="d-flex gap-2">
                    <input type="text" id="searchTable" class="form-control form-control-sm" placeholder="Buscar en tabla..." style="width:250px;">
                    <small class="text-muted align-self-center">{{ count($dataTable) }} registros</small>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                    <table class="table table-sm table-hover table-striped" id="dataTable">
                        <thead class="table-dark" style="position:sticky; top:0;">
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Usuario / Origen</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataTable as $row)
                            <tr>
                                <td>{{ $row['fecha'] ?? '-' }}</td>
                                <td><span class="badge badge-{{ $row['tipo_badge'] ?? 'secondary' }}">{{ $row['tipo'] ?? '-' }}</span></td>
                                <td>{{ $row['descripcion'] ?? '-' }}</td>
                                <td>{{ $row['origen'] ?? '-' }}</td>
                                <td>{{ $row['estado'] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<?php
// Pre-process data for charts
$turnosLabels = $turnos->keys()->map(function($v) { return ucfirst($v); })->values();
$turnosData = $turnos->values();
$areasLabels = $trabajadoresPorArea->pluck('label');
$areasData = $trabajadoresPorArea->pluck('value');
$horasLabels = $horasPico->keys()->map(function($v) { return sprintf('%02d:00', $v); })->values();
$horasData = $horasPico->values();
$gravedadLabels = $incidentesPorGravedad->keys()->map(function($v) { return ucfirst($v); })->values();
$gravedadData = $incidentesPorGravedad->values();
?>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">trending_up</i> Ingresos/Salidas {{ request('desde') ? '— ' . request('desde') . ' a ' . request('hasta', date('Y-m-d')) : '— Últimos 7 Días' }}</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chart7d" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallback7d">Gráfico no disponible</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">pie_chart</i> Trabajadores por Turno</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chartTurnos" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallbackTurnos">Gráfico no disponible</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">assessment</i> Trabajadores por Área</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chartAreas" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallbackAreas">Gráfico no disponible</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">access_time</i> Ingresos por Hora {{ request('desde') ? '— ' . request('desde') . ' a ' . request('hasta', date('Y-m-d')) : '— Hoy' }}</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chartHoras" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallbackHoras">Gráfico no disponible</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">error</i> Incidentes por Gravedad</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chartGravedad" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallbackGravedad">Gráfico no disponible</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">date_range</i> Incidentes {{ request('desde') ? '— ' . request('desde') . ' a ' . request('hasta', date('Y-m-d')) : '— Últimos 6 Meses' }}</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chartIncMes" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallbackIncMes">Gráfico no disponible</div>
            </div>
        </div>
    </div>
</div>

<!-- Alertas section -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">notifications_active</i> Alertas por Tipo</h4>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <h1 class="text-danger font-weight-bold" style="font-size: 2.5rem;">{{ $alertasPeriodo }}</h1>
                        <small class="text-muted">Alertas totales</small>
                    </div>
                    <div class="col-6">
                        <h1 class="text-danger font-weight-bold" style="font-size: 2.5rem;">{{ $alertasCriticas }}</h1>
                        <small class="text-muted">Alertas críticas</small>
                    </div>
                </div>
                <div class="chart-container"><canvas id="chartAlertasTipo" style="height:250px;"></canvas></div>
                <div class="chart-fallback" id="fallbackAlertasTipo">Gráfico no disponible</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">warning</i> Últimas Alertas</h4>
            </div>
            <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                @php
                    $ultimasAlertas = \App\Models\Alerta::orderBy('fecha_alerta', 'desc')->limit(10)->get();
                @endphp
                @forelse($ultimasAlertas as $alerta)
                    <div class="d-flex align-items-start mb-2 p-2 rounded" style="background: rgba(255,0,0,0.08); border-left: 4px solid {{ $alerta->nivel === 'critico' ? 'var(--porco-critical)' : ($alerta->nivel === 'alto' ? 'var(--porco-warning)' : 'var(--porco-info)') }};">
                        <i class="material-icons mr-2" style="color: {{ $alerta->nivel === 'critico' ? 'var(--porco-critical)' : 'var(--porco-warning)' }};">{{ $alerta->nivel === 'critico' ? 'warning' : 'info' }}</i>
                        <div class="flex-grow-1">
                            <small class="font-weight-bold">{{ $alerta->origen }}</small><br>
                            <small>{{ Str::limit($alerta->mensaje, 60) }}</small>
                            <br><small class="text-muted">{{ $alerta->fecha_alerta->format('d/m H:i') }}</small>
                        </div>
                        <span class="badge badge-{{ $alerta->nivel === 'critico' ? 'danger' : ($alerta->nivel === 'alto' ? 'warning' : 'info') }} badge-pill">{{ $alerta->nivel }}</span>
                    </div>
                @empty
                    <p class="text-muted text-center">No hay alertas registradas</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function loadChartJS(callback) {
        if (typeof Chart !== 'undefined') { callback(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        s.onload = callback;
        s.onerror = function() {
            document.querySelectorAll('.chart-container').forEach(function(el) { el.style.display = 'none'; });
            document.querySelectorAll('.chart-fallback').forEach(function(el) { el.style.display = 'block'; });
        };
        document.head.appendChild(s);
    }

    loadChartJS(function() {
        var cssColor = function (token) {
            return getComputedStyle(document.documentElement).getPropertyValue(token).trim();
        };
        var textColor = cssColor('--porco-ink');
        var gridColor = 'rgba(66,23,44,0.12)';

        var chartDefaults = {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color: textColor, font: { size: 12 } } } }
        };
        var scales = {
            x: { ticks: { color: textColor }, grid: { color: gridColor } },
            y: { ticks: { color: textColor }, grid: { color: gridColor } }
        };

        try {
            new Chart(document.getElementById('chart7d'), {
                type: 'bar',
                data: {
                    labels: @json($labels7d),
                    datasets: [
                        { label: 'Ingresos', data: @json($ingresos7d), backgroundColor: cssColor('--porco-wine'), borderRadius: 6 },
                        { label: 'Salidas', data: @json($salidas7d), backgroundColor: cssColor('--porco-coral'), borderRadius: 6 }
                    ]
                },
                options: Object.assign({}, chartDefaults, { scales: scales, plugins: { legend: { labels: { color: textColor, font: { size: 12 } }, position: 'top' } } })
            });
        } catch(e) { document.getElementById('fallback7d').style.display = 'block'; }

        try {
            new Chart(document.getElementById('chartTurnos'), {
                type: 'doughnut',
                data: {
                    labels: @json($turnosLabels),
                    datasets: [{ data: @json($turnosData), backgroundColor: [cssColor('--porco-wine'), cssColor('--porco-raspberry'), cssColor('--porco-gold')], borderWidth: 0 }]
                },
                options: Object.assign({}, chartDefaults, { plugins: { legend: { labels: { color: textColor, font: { size: 12 } }, position: 'bottom' } } })
            });
        } catch(e) { document.getElementById('fallbackTurnos').style.display = 'block'; }

        try {
            new Chart(document.getElementById('chartAreas'), {
                type: 'bar',
                data: {
                    labels: @json($areasLabels),
                    datasets: [{ label: 'Trabajadores', data: @json($areasData), backgroundColor: cssColor('--porco-raspberry'), borderRadius: 6 }]
                },
                options: Object.assign({}, chartDefaults, { scales: scales, indexAxis: 'y', plugins: { legend: { display: false } } })
            });
        } catch(e) { document.getElementById('fallbackAreas').style.display = 'block'; }

        try {
            new Chart(document.getElementById('chartHoras'), {
                type: 'line',
                data: {
                    labels: @json($horasLabels),
                    datasets: [{ label: 'Ingresos', data: @json($horasData), borderColor: cssColor('--porco-raspberry'), backgroundColor: 'rgba(197,45,89,0.15)', fill: true, tension: 0.4, pointRadius: 4 }]
                },
                options: Object.assign({}, chartDefaults, { scales: scales, plugins: { legend: { display: false } } })
            });
        } catch(e) { document.getElementById('fallbackHoras').style.display = 'block'; }

        try {
            new Chart(document.getElementById('chartGravedad'), {
                type: 'doughnut',
                data: {
                    labels: @json($gravedadLabels),
                    datasets: [{ data: @json($gravedadData), backgroundColor: [cssColor('--porco-warning'), cssColor('--porco-raspberry'), cssColor('--porco-anomaly'), cssColor('--porco-critical')], borderWidth: 0 }]
                },
                options: Object.assign({}, chartDefaults, { plugins: { legend: { labels: { color: textColor, font: { size: 12 } }, position: 'bottom' } } })
            });
        } catch(e) { document.getElementById('fallbackGravedad').style.display = 'block'; }

        try {
            new Chart(document.getElementById('chartIncMes'), {
                type: 'line',
                data: {
                    labels: @json($labels6m),
                    datasets: [{ label: 'Incidentes', data: @json($incidentes6m), borderColor: cssColor('--porco-raspberry'), backgroundColor: 'rgba(197,45,89,0.12)', fill: true, tension: 0.4, pointRadius: 4 }]
                },
                options: Object.assign({}, chartDefaults, { scales: scales, plugins: { legend: { display: false } } })
            });
        } catch(e) { document.getElementById('fallbackIncMes').style.display = 'block'; }

        try {
            var alertasLabels = @json($alertasPorTipo->keys());
            var alertasData = @json($alertasPorTipo->values());
            if (alertasLabels.length > 0) {
                new Chart(document.getElementById('chartAlertasTipo'), {
                    type: 'doughnut',
                    data: {
                        labels: alertasLabels,
                        datasets: [{ data: alertasData, backgroundColor: [cssColor('--porco-critical'), cssColor('--porco-warning'), cssColor('--porco-info'), cssColor('--porco-normal'), cssColor('--porco-anomaly')], borderWidth: 0 }]
                    },
                    options: Object.assign({}, chartDefaults, { plugins: { legend: { labels: { color: textColor, font: { size: 12 } }, position: 'bottom' } } })
                });
            } else {
                document.getElementById('fallbackAlertasTipo').style.display = 'block';
                document.getElementById('fallbackAlertasTipo').textContent = 'Sin datos de alertas';
            }
        } catch(e) { document.getElementById('fallbackAlertasTipo').style.display = 'block'; }
    });
});

// Tabla de datos: búsqueda en tabla
var searchInput = document.getElementById('searchTable');
if (searchInput) {
    searchInput.addEventListener('keyup', function() {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#dataTable tbody tr').forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}
</script>
@endpush
@endsection
