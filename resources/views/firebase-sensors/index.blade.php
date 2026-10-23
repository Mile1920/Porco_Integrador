@extends('layouts.app')
@section('title', 'Sensores Firebase')
@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0">
                        <i class="fas fa-cloud"></i> Sensores Firebase
                    </h3>
                    <small class="text-muted">Monitoreo en tiempo real desde Firebase</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                    <button class="btn btn-outline-light btn-sm" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $totalSensors }}</h5>
                                <p class="card-text">Sensores Activos</p>
                                <i class="fas fa-microchip fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $totalReadings }}</h5>
                                <p class="card-text">Total Lecturas</p>
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $alerts }}</h5>
                                <p class="card-text">Alertas</p>
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title" id="last-update">Ahora</h5>
                                <p class="card-text">Última Actualización</p>
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Sensores -->
                <div class="row">
                    @if($sensorData)
                        @foreach($sensorData as $sensorId => $sensor)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 {{ isset($sensor['activo']) && $sensor['activo'] ? 'border-success' : 'border-secondary' }}">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-sensor"></i>
                                            {{ $sensor['nombre'] ?? 'Sensor ' . $sensorId }}
                                        </h5>
                                        <small class="text-muted">ID: {{ $sensorId }}</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <span class="badge {{ isset($sensor['activo']) && $sensor['activo'] ? 'badge-success' : 'badge-secondary' }}">
                                                {{ isset($sensor['activo']) && $sensor['activo'] ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>

                                        @if(isset($sensor['lecturas']) && is_array($sensor['lecturas']))
                                            <div class="mb-3">
                                                <h6>Lecturas Recientes:</h6>
                                                <div class="small">
                                                    @php
                                                        $recentReadings = array_slice($sensor['lecturas'], -3, 3, true);
                                                    @endphp
                                                    @foreach($recentReadings as $timestamp => $reading)
                                                        <div class="d-flex justify-content-between">
                                                            <span>{{ \Carbon\Carbon::createFromTimestamp($timestamp/1000)->format('H:i:s') }}</span>
                                                            <span class="font-weight-bold">
                                                                @if(isset($reading['temperatura']))
                                                                    {{ $reading['temperatura'] }}°C
                                                                @elseif(isset($reading['humedad']))
                                                                    {{ $reading['humedad'] }}%
                                                                @elseif(isset($reading['pm2_5']))
                                                                    {{ $reading['pm2_5'] }} µg/m³
                                                                @else
                                                                    {{ json_encode($reading) }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if(isset($sensor['alertas']) && count($sensor['alertas']) > 0)
                                            <div class="alert alert-warning py-2">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ count($sensor['alertas']) }} alerta(s) activa(s)
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('firebase-sensors.show', $sensorId) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No se encontraron datos de sensores en Firebase. Verifica la conexión y configuración.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let refreshInterval;

function refreshData() {
    location.reload();
}

function startAutoRefresh() {
    refreshInterval = setInterval(() => {
        fetch('{{ route("api.firebase-sensors") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
                    // Aquí podrías actualizar los datos dinámicamente
                    console.log('Datos actualizados:', data.data);
                }
            })
            .catch(error => {
                console.error('Error actualizando datos:', error);
            });
    }, 30000); // Actualizar cada 30 segundos
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

// Iniciar actualización automática cuando la página cargue
document.addEventListener('DOMContentLoaded', startAutoRefresh);

// Detener cuando la página se cierre
window.addEventListener('beforeunload', stopAutoRefresh);
</script>
@endpush
@endsection
