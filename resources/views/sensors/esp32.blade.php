@extends('layouts.app')
@section('title', 'ESP32 - Sensor Remoto')
@section('content')

<style>
#esp32Status { transition: all 0.3s ease; }
#esp32Status .status-dot {
    width: 14px; height: 14px; border-radius: 50%; display: inline-block; margin-right: 8px; animation: pulse 2s infinite;
}
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
</style>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-0">ESP32 — Sensor Remoto</h4>
                    <p class="card-category mb-0">Monitoreo del dispositivo ESP32 en la mina</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-light btn-sm" onclick="location.reload()">
                        <i class="material-icons" style="vertical-align: middle; font-size: 1rem;">refresh</i> Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Estado del Dispositivo</h4>
            </div>
            <div class="card-body text-center py-4" id="esp32Status">
                <div class="mb-3">
                    <span class="status-dot" id="statusDot" style="background: var(--porco-warning);"></span>
                    <span id="statusText" style="font-size: 1.2rem; font-weight: 600;">Verificando...</span>
                </div>
                <div id="latencyInfo" class="mb-3" style="display:none;">
                    <small class="text-muted">Latencia: <span id="latencyMs">-</span> ms</small>
                </div>
                <div class="d-flex justify-content-center gap-3 mb-3">
                    <button class="btn btn-success" id="btnConnect" onclick="conectarESP32()" disabled>Conectar</button>
                    <button class="btn btn-danger" id="btnDisconnect" onclick="desconectarESP32()" disabled>Desconectar</button>
                </div>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                    Última verificación: <span id="lastCheck">-</span>
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Configuración</h4>
                <button class="btn btn-sm btn-outline-primary" id="btnEditConfig" onclick="editarConfig()">Editar</button>
            </div>
            <div class="card-body">
                <form id="esp32ConfigForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Dirección IP</label>
                        <input type="text" name="ip" id="esp32Ip" class="form-control" value="{{ $ip }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección MAC</label>
                        <input type="text" name="mac" id="esp32Mac" class="form-control" value="{{ $mac }}" readonly>
                    </div>
                    <div id="configButtons" style="display:none;">
                        <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="cancelarEditar()">Cancelar</button>
                        <small id="configMsg" class="ms-2"></small>
                    </div>
                    <div id="configInfo" class="mt-2">
                        <small class="text-muted">Última lectura: {{ $ultimaConexion ? $ultimaConexion->diffForHumans() : 'Nunca' }}</small><br>
                        <small class="text-muted">Total lecturas: {{ $lecturas->count() }}</small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Últimas Lecturas</h4>
            </div>
            <div class="card-body">
                @if($lecturas->isEmpty())
                    <div class="alert alert-info mb-0">
                        No se han recibido lecturas del ESP32. Conecta el dispositivo para comenzar a recibir datos.
                    </div>
                @else
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table">
                            <thead>
                                <tr><th>ID</th><th>Tipo</th><th>Payload</th><th>Recibido</th></tr>
                            </thead>
                            <tbody>
                                @foreach($lecturas as $l)
                                <tr>
                                    <td>{{ $l->id }}</td>
                                    <td><span class="badge badge-info">{{ $l->tipo ?? 'N/A' }}</span></td>
                                    <td>
                                        @if(is_array($l->payload))
                                            @foreach($l->payload as $k => $v)
                                                <span class="badge badge-secondary me-1">{{ $k }}: {{ is_array($v) ? json_encode($v) : $v }}</span>
                                            @endforeach
                                        @else
                                            <code>{{ json_encode($l->payload) }}</code>
                                        @endif
                                    </td>
                                    <td>{{ $l->recibido_en ? $l->recibido_en->format('d/m/Y H:i:s') : $l->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() { verificarESP32(); });

var esp32Conectado = false;
var intervaloVerificacion = null;

function verificarESP32() {
    var btnC = document.getElementById('btnConnect');
    var btnD = document.getElementById('btnDisconnect');
    var dot = document.getElementById('statusDot');
    var text = document.getElementById('statusText');
    var latDiv = document.getElementById('latencyInfo');
    var latMs = document.getElementById('latencyMs');
    var lastCheck = document.getElementById('lastCheck');

    btnC.disabled = true; btnD.disabled = true;
    dot.style.background = 'var(--porco-warning)';
    text.textContent = 'Verificando...';

    fetch('{{ route("api.sensor.esp32.status") }}')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            lastCheck.textContent = new Date().toLocaleTimeString();
            esp32Conectado = data.connected;
            if (data.connected) {
                dot.style.background = 'var(--porco-normal)';
                text.textContent = 'Conectado';
                btnC.disabled = true; btnD.disabled = false;
                if (data.latency_ms) { latDiv.style.display = 'block'; latMs.textContent = data.latency_ms; }
            } else {
                dot.style.background = 'var(--porco-critical)';
                text.textContent = 'Desconectado';
                btnC.disabled = false; btnD.disabled = true;
                latDiv.style.display = 'none';
            }
        })
        .catch(function() {
            lastCheck.textContent = new Date().toLocaleTimeString();
            dot.style.background = 'var(--porco-critical)';
            text.textContent = 'Desconectado';
            btnC.disabled = false; btnD.disabled = true;
            esp32Conectado = false;
        });
}

function conectarESP32() {
    verificarESP32();
    if (intervaloVerificacion) clearInterval(intervaloVerificacion);
    intervaloVerificacion = setInterval(verificarESP32, 10000);
}

function desconectarESP32() {
    if (intervaloVerificacion) { clearInterval(intervaloVerificacion); intervaloVerificacion = null; }
    esp32Conectado = false;
    document.getElementById('statusDot').style.background = 'var(--porco-critical)';
    document.getElementById('statusText').textContent = 'Desconectado';
    document.getElementById('btnConnect').disabled = false;
    document.getElementById('btnDisconnect').disabled = true;
    document.getElementById('latencyInfo').style.display = 'none';
}

function editarConfig() {
    document.getElementById('esp32Ip').readOnly = false;
    document.getElementById('esp32Mac').readOnly = false;
    document.getElementById('configButtons').style.display = 'block';
    document.getElementById('btnEditConfig').style.display = 'none';
}

function cancelarEditar() {
    document.getElementById('esp32Ip').readOnly = true;
    document.getElementById('esp32Mac').readOnly = true;
    document.getElementById('configButtons').style.display = 'none';
    document.getElementById('btnEditConfig').style.display = 'inline-block';
    document.getElementById('esp32Ip').value = '{{ $ip }}';
    document.getElementById('esp32Mac').value = '{{ $mac }}';
}

document.getElementById('esp32ConfigForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    var msg = document.getElementById('configMsg');
    btn.disabled = true; msg.textContent = 'Guardando...';

    fetch('{{ route("api.sensor.esp32.config") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ ip: document.getElementById('esp32Ip').value, mac: document.getElementById('esp32Mac').value })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            msg.textContent = 'Guardado correctamente';
            msg.style.color = 'var(--porco-normal)';
            cancelarEditar();
            setTimeout(function() { msg.textContent = ''; }, 3000);
        } else {
            msg.textContent = 'Error al guardar';
            msg.style.color = 'var(--porco-critical)';
        }
    })
    .catch(function() {
        msg.textContent = 'Error de conexión';
        msg.style.color = 'var(--porco-critical)';
    })
    .finally(function() { btn.disabled = false; });
});
</script>
@endpush
@endsection
