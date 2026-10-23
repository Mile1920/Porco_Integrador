@extends('layouts.app')
@section('title', 'Sensores - Datos')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2>Datos de Sensores</h2>
            <p class="text-muted mb-0">Últimos 50 registros</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
            <a href="{{ route('sensors.devices.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-cogs"></i> Dispositivos
            </a>
        </div>
    </div>

    @if($datos->isEmpty())
        <div class="alert alert-info">No hay datos recientes de sensores.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Device</th>
                        <th>Tipo</th>
                        <th>Payload</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos as $d)
                    <tr>
                        <td>{{ $d->id }}</td>
                        <td>{{ $d->device_id }}</td>
                        <td>{{ $d->tipo }}</td>
                        <td><pre class="sensor-payload">{{ json_encode($d->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></td>
                        <td>{{ $d->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <a href="{{ route('business.flow') }}" class="btn btn-secondary">Volver a Módulos</a>
</div>

<!-- Tabla de lecturas ESP32 -->
<div class="container mt-4">
    <div class="card">
        <div class="card-header card-header-info d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="fas fa-table"></i> Últimas lecturas de sensores
            </h5>
            <small class="text-white-50">{{ count($esp32Lecturas ?? []) }} registros</small>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="max-height:300px; overflow-y:auto;">
                <table class="table table-sm table-hover table-striped">
                    <thead class="table-info" style="position:sticky; top:0;">
                        <tr>
                            <th>Hora</th>
                            <th>MQ-7 (CO)</th>
                            <th>MQ-135 (Aire)</th>
                            <th>Temperatura</th>
                            <th>Humedad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($esp32Lecturas ?? [] as $lectura)
                        <tr class="{{ ($lectura->payload['alerta'] ?? false) ? 'table-danger' : '' }}">
                            <td class="text-primary">{{ $lectura->created_at->format('H:i:s') }}</td>
                            <td>
                                @if(is_array($lectura->payload))
                                    <span class="badge {{ ($lectura->payload['mq7_co'] ?? 0) > 2200 ? 'badge-danger' : 'badge-secondary' }}">
                                        {{ $lectura->payload['mq7_co'] ?? '?' }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(is_array($lectura->payload))
                                    <span class="badge {{ ($lectura->payload['mq135_aire'] ?? 0) > 2850 ? 'badge-danger' : 'badge-secondary' }}">
                                        {{ $lectura->payload['mq135_aire'] ?? '?' }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ is_array($lectura->payload) ? ($lectura->payload['temperatura'] ?? '-') : '-' }}</td>
                            <td>{{ is_array($lectura->payload) ? ($lectura->payload['humedad'] ?? '-') : '-' }}</td>
                            <td>
                                @if($lectura->payload['alerta'] ?? false)
                                    <span class="badge badge-danger">⚠ ALERTA</span>
                                @else
                                    <span class="badge badge-success">Normal</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                <i class="fas fa-info-circle"></i> No hay lecturas aún. Conectá el ESP32 para ver datos.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ESP32 Config -->
<div class="container mt-4">
    <div class="card">
        <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="fas fa-microchip"></i> ESP32 — Configuración y Estado
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-success btn-sm" onclick="conectarEsp32()">
                    <i class="fas fa-plug"></i> Conectar
                </button>
                <button class="btn btn-danger btn-sm" onclick="desconectarEsp32()">
                    <i class="fas fa-power-off"></i> Desconectar
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <label class="form-label">Dirección IP</label>
                    <input type="text" id="esp32Ip" class="form-control" value="{{ $esp32Ip ?? config('esp32.ip', env('ESP32_IP', '192.168.1.205')) }}" placeholder="192.168.1.205">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Dirección MAC</label>
                    <input type="text" id="esp32Mac" class="form-control" value="{{ $esp32Mac ?? config('esp32.mac', env('ESP32_MAC', '00:4B:12:35:3E:00')) }}" placeholder="00:4B:12:35:3E:00">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100" onclick="saveEsp32Config()">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div id="esp32Status" class="p-3 rounded text-center" style="background: #f5f5f5;">
                        <h6>Estado</h6>
                        <div id="esp32StatusText" class="text-muted">
                            <i class="fas fa-circle-notch fa-spin"></i> Verificando...
                        </div>
                        <small id="esp32Latency" class="text-muted"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Últimas Lecturas</h6>
                    <div id="esp32Lecturas" style="max-height: 200px; overflow-y: auto;">
                        @forelse($esp32Lecturas ?? [] as $lectura)
                            <div class="small border-bottom py-1">
                                <span class="text-primary">{{ $lectura->created_at->format('H:i:s') }}</span>
                                @if($lectura->tipo === 'gases_toxicos' && is_array($lectura->payload))
                                    <span class="badge badge-secondary">MQ-7: {{ $lectura->payload['mq7_co'] ?? '?' }}</span>
                                    <span class="badge badge-secondary">MQ-135: {{ $lectura->payload['mq135_aire'] ?? '?' }}</span>
                                    @if($lectura->payload['alerta'] ?? false)
                                        <span class="badge badge-danger">ALERTA</span>
                                    @endif
                                @elseif(is_array($lectura->payload))
                                    @foreach($lectura->payload as $k => $v)
                                        <span class="badge badge-secondary me-1">{{ $k }}: {{ is_array($v) ? json_encode($v) : $v }}</span>
                                    @endforeach
                                @else
                                    <span>{{ $lectura->tipo }}: {{ json_encode($lectura->payload) }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="text-muted small">Sin lecturas aún</div>
                        @endforelse
                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                    </div>
                                </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function verificarEstadoEsp32() {
    fetch('{{ route("api.sensor.esp32.status") }}')
        .then(r => r.json())
        .then(data => actualizarUIEsp32(data))
        .catch(() => actualizarUIEsp32({ connected: false }));
}

function conectarEsp32() {
    document.getElementById('esp32StatusText').innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Conectando...';

    fetch('{{ route("api.sensor.esp32.connect") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(r => r.json())
    .then(data => {
        actualizarUIEsp32(data);
        if (data.connected) {
            alert('ESP32 conectado');
        } else {
            alert('No se pudo conectar: ' + (data.error || 'sin respuesta'));
        }
    })
    .catch(() => alert('Error de red'));
}

function desconectarEsp32() {
    fetch('{{ route("api.sensor.esp32.disconnect") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(r => r.json())
    .then(data => {
        actualizarUIEsp32({ connected: false });
        alert('ESP32 desconectado');
    })
    .catch(() => alert('Error de red'));
}

function actualizarUIEsp32(data) {
    const statusEl = document.getElementById('esp32StatusText');
    const latencyEl = document.getElementById('esp32Latency');
    const statusDiv = document.getElementById('esp32Status');

    if (data.connected) {
        statusEl.innerHTML = '<span style="color:var(--porco-normal);font-size:1.2rem"><i class="fas fa-check-circle"></i> Conectado</span>';
        statusDiv.style.background = '#e8f5e9';
        if (data.ultima_conexion) {
            const d = new Date(data.ultima_conexion);
            latencyEl.textContent = 'Último dato: ' + d.toLocaleTimeString() + ' (' + data.minutos_sin_datos + ' min atrás)';
        } else {
            latencyEl.textContent = '';
        }
    } else {
        statusEl.innerHTML = '<span style="color:var(--porco-critical);font-size:1.2rem"><i class="fas fa-times-circle"></i> Desconectado</span>';
        statusDiv.style.background = '#ffebee';
        if (data.minutos_sin_datos !== null && data.minutos_sin_datos !== undefined) {
            latencyEl.textContent = 'Sin datos desde hace ' + data.minutos_sin_datos + ' min';
        } else {
            latencyEl.textContent = 'El ESP32 no responde. Verificá que esté encendido y en la misma red.';
        }
    }
}

function saveEsp32Config() {
    const ip = document.getElementById('esp32Ip').value.trim();
    const mac = document.getElementById('esp32Mac').value.trim();
    if (!ip || !mac) { alert('IP y MAC requeridos'); return; }

    fetch('{{ route("api.sensor.esp32.config") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ ip, mac })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Configuración guardada');
        } else {
            alert('Error: ' + (data.message || 'desconocido'));
        }
    })
    .catch(() => alert('Error de red'));
}

verificarEstadoEsp32();
setInterval(verificarEstadoEsp32, 15000);
</script>
@endpush
@endsection
