@extends('layouts.app')
@section('title', 'Detalles del Sensor Firebase')
@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0">
                        <i class="fas fa-sensor"></i> Detalles del Sensor: {{ $sensor['nombre'] ?? 'Sensor ' . $sensorId }}
                    </h3>
                    <small class="text-muted">ID: {{ $sensorId }}</small>
                </div>
                <div>
                    <a href="{{ route('firebase-sensors.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <button class="btn btn-outline-light btn-sm" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Información del Sensor -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Información General</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>ID del Sensor:</strong></td>
                                        <td>{{ $sensorId }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nombre:</strong></td>
                                        <td>{{ $sensor['nombre'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Estado:</strong></td>
                                        <td>
                                            <span class="badge {{ isset($sensor['activo']) && $sensor['activo'] ? 'badge-success' : 'badge-secondary' }}">
                                                {{ isset($sensor['activo']) && $sensor['activo'] ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Área:</strong></td>
                                        <td>{{ $sensor['area'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tipo:</strong></td>
                                        <td>{{ $sensor['tipo'] ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Estadísticas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h4 class="text-primary">{{ count($recentReadings ?? []) }}</h4>
                                        <small class="text-muted">Lecturas Recientes</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-warning">{{ count($sensor['alertas'] ?? []) }}</h4>
                                        <small class="text-muted">Alertas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Lecturas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Gráfico de Lecturas Recientes</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="sensorChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- Tabla de Lecturas Recientes -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Lecturas Recientes</h5>
                    </div>
                    <div class="card-body">
                        @if($recentReadings && count($recentReadings) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha/Hora</th>
                                            <th>Temperatura (°C)</th>
                                            <th>Humedad (%)</th>
                                            <th>PM2.5 (µg/m³)</th>
                                            <th>Otros Datos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(array_reverse($recentReadings, true) as $timestamp => $reading)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::createFromTimestamp($timestamp/1000)->format('d/m/Y H:i:s') }}</td>
                                                <td>{{ $reading['temperatura'] ?? '-' }}</td>
                                                <td>{{ $reading['humedad'] ?? '-' }}</td>
                                                <td>{{ $reading['pm2_5'] ?? '-' }}</td>
                                                <td>
                                                    @if(count($reading) > 3)
                                                        <details>
                                                            <summary>Ver más</summary>
                                                            <pre class="small">{{ json_encode($reading, JSON_PRETTY_PRINT) }}</pre>
                                                        </details>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No hay lecturas recientes disponibles para este sensor.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Alertas -->
                @if(isset($sensor['alertas']) && count($sensor['alertas']) > 0)
                    <div class="card mt-4">
                        <div class="card-header bg-warning">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-exclamation-triangle"></i> Alertas Activas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @foreach($sensor['alertas'] as $alerta)
                                    <div class="list-group-item list-group-item-warning">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $alerta['mensaje'] ?? 'Alerta' }}</h6>
                                            <small>{{ isset($alerta['timestamp']) ? \Carbon\Carbon::createFromTimestamp($alerta['timestamp']/1000)->diffForHumans() : 'Ahora' }}</small>
                                        </div>
                                        <p class="mb-1">{{ $alerta['descripcion'] ?? '' }}</p>
                                        <small class="text-muted">Nivel: {{ $alerta['nivel'] ?? 'Medio' }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let sensorChart;

function refreshData() {
    location.reload();
}

function initChart() {
    const ctx = document.getElementById('sensorChart').getContext('2d');

    // Preparar datos para el gráfico
    const readings = @json($recentReadings ?? []);
    const labels = [];
    const tempData = [];
    const humidityData = [];
    const pm25Data = [];

    // Ordenar por timestamp
    const sortedReadings = Object.keys(readings)
        .sort((a, b) => parseInt(a) - parseInt(b))
        .reduce((obj, key) => {
            obj[key] = readings[key];
            return obj;
        }, {});

    Object.keys(sortedReadings).forEach(timestamp => {
        const reading = sortedReadings[timestamp];
        const date = new Date(parseInt(timestamp));

        labels.push(date.toLocaleTimeString());
        tempData.push(reading.temperatura ?? null);
        humidityData.push(reading.humedad ?? null);
        pm25Data.push(reading.pm2_5 ?? null);
    });

    sensorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Temperatura (°C)',
                data: tempData,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }, {
                label: 'Humedad (%)',
                data: humidityData,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1
            }, {
                label: 'PM2.5 (µg/m³)',
                data: pm25Data,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Lecturas del Sensor - Últimas 50 mediciones'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', initChart);
</script>
@endpush
@endsection
