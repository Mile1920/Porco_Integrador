@extends('layouts.app')
@section('title', 'Dashboard de Sensores')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm me-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <h3 class="card-title mb-0 d-inline">
                        <i class="fas fa-chart-line"></i> Dashboard de Sensores
                    </h3>
                </div>
                <div>
                    @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                        <a href="{{ route('firebase-sensors.create') }}" class="btn btn-success btn-sm me-2">
                            <i class="fas fa-plus"></i> Agregar Sensor
                        </a>
                        <span class="badge badge-success">Panel de Control Completo</span>
                    @else
                        <span class="badge badge-info">Vista de Solo Lectura</span>
                    @endif
                </div>
            </div>
            </div>
            <div class="card-body">
                <!-- Filtros Interactivos -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-filter"></i> Filtros de Monitoreo
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Tipo de Sensor</label>
                                        <select id="tipoFilter" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="movimiento_tierra">Movimiento Tierra</option>
                                            <option value="gases_toxicos">Gases Tóxicos</option>
                                            <option value="signos_vitales">Signos Vitales</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Estado</label>
                                        <select id="estadoFilter" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                            <option value="alerta">Con Alerta</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Área</label>
                                        <select id="areaFilter" class="form-control">
                                            <option value="">Todas</option>
                                            @foreach($areas ?? [] as $area)
                                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Actualizar</label>
                                        <button id="refreshBtn" class="btn btn-primary form-control">
                                            <i class="fas fa-sync-alt"></i> Actualizar Datos
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas Claras y Entendibles -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card card-stats">
                            <div class="card-body text-center">
                                <div class="icon-big text-center icon-success" style="font-size: 3rem; margin-bottom: 15px;">
                                    <i class="fas fa-sensor"></i>
                                </div>
                                <p class="card-category" style="font-size: 1.2rem; font-weight: bold;">Sensores Activos</p>
                                <h3 class="card-title" style="font-size: 3rem; font-weight: bold; color: var(--porco-normal);">{{ $totalSensors }}</h3>
                            </div>
                            <div class="card-footer text-center">
                                <div class="stats">
                                    <small class="text-muted">Total Conectados</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stats">
                            <div class="card-body text-center">
                                <div class="icon-big text-center icon-info" style="font-size: 3rem; margin-bottom: 15px;">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <p class="card-category" style="font-size: 1.2rem; font-weight: bold;">Lecturas Totales</p>
                                <h3 class="card-title" style="font-size: 3rem; font-weight: bold; color: var(--porco-info);">{{ $totalReadings }}</h3>
                            </div>
                            <div class="card-footer text-center">
                                <div class="stats">
                                    <small class="text-muted">Últimas 24h</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stats">
                            <div class="card-body text-center">
                                <div class="icon-big text-center icon-warning" style="font-size: 3rem; margin-bottom: 15px;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <p class="card-category" style="font-size: 1.2rem; font-weight: bold;">Alertas</p>
                                <h3 class="card-title" style="font-size: 3rem; font-weight: bold; color: var(--porco-warning);">{{ $alerts }}</h3>
                            </div>
                            <div class="card-footer text-center">
                                <div class="stats">
                                    <small class="text-muted">Activas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stats">
                            <div class="card-body text-center">
                                <div class="icon-big text-center icon-warning" style="font-size: 3rem; margin-bottom: 15px;">
                                    <i class="fas fa-thermometer-half"></i>
                                </div>
                                <p class="card-category" style="font-size: 1.2rem; font-weight: bold;">Temp. Promedio</p>
                                <h3 class="card-title" style="font-size: 3rem; font-weight: bold; color: var(--porco-warning);">{{ $avgTemperature === null ? '—' : number_format($avgTemperature, 1).'°C' }}</h3>
                            </div>
                            <div class="card-footer text-center">
                                <div class="stats">
                                    <small class="text-muted">Actual</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de lecturas ESP32 (sobre el panel de configuración) -->
                <div class="row mb-4">
                    <div class="col-md-12">
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
                                                        <span class="badge {{ !isset($lectura->payload['mq7_co']) ? 'badge-secondary' : ($lectura->payload['mq7_co'] > 2200 ? 'badge-danger' : 'badge-success') }}">
                                                            {{ $lectura->payload['mq7_co'] ?? 'Sin dato' }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(is_array($lectura->payload))
                                                        <span class="badge {{ !isset($lectura->payload['mq135_aire']) ? 'badge-secondary' : ($lectura->payload['mq135_aire'] > 2850 ? 'badge-danger' : 'badge-success') }}">
                                                            {{ $lectura->payload['mq135_aire'] ?? 'Sin dato' }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ is_array($lectura->payload) ? ($lectura->payload['temperatura'] ?? '-') : '-' }}</td>
                                                <td>{{ is_array($lectura->payload) ? ($lectura->payload['humedad'] ?? '-') : '-' }}</td>
                                                <td>
                                                    @if(!is_array($lectura->payload) || !array_key_exists('alerta', $lectura->payload))
                                                        <span class="badge badge-secondary">Sin estado</span>
                                                    @elseif($lectura->payload['alerta'])
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
                </div>

                <!-- Configuración ESP32 -->
                <div class="row mb-4">
                    <div class="col-md-12">
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

                <!-- Sensores Locales (Tabla) -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-database"></i> Sensores en Base de Datos
                                </h5>
                                <div>
                                    <a href="{{ route('sensors.devices.index') }}" class="btn btn-outline-light btn-sm">
                                        <i class="fas fa-cogs"></i> Ver Todos / Editar
                                    </a>
                                    <a href="{{ route('sensors.devices.create') }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus"></i> Agregar
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($localSensores->isEmpty())
                                    <div class="alert alert-info mb-0">No hay sensores registrados en la base de datos local.</div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Device ID</th>
                                                    <th>Nombre</th>
                                                    <th>Área</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($localSensores as $s)
                                                <tr>
                                                    <td>{{ $s->id }}</td>
                                                    <td>{{ $s->device_id }}</td>
                                                    <td>{{ $s->nombre ?? '-' }}</td>
                                                    <td>{{ $s->area->nombre ?? 'Sin área' }}</td>
                                                    <td>
                                                        @if($s->estado === 'activo')
                                                            <span class="badge badge-success">Activo</span>
                                                        @elseif($s->estado === 'alerta')
                                                            <span class="badge badge-warning">Alerta</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactivo</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('sensors.devices.edit', $s) }}" class="btn btn-sm btn-warning" title="Editar / Cambiar Área">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-2 text-muted small">
                                        <i class="fas fa-info-circle"></i> Total: {{ $localSensores->count() }} sensor(es)
                                        @if(count($activeSensors ?? []) > 0)
                                            | <i class="fas fa-fire"></i> Firebase activo: {{ count($activeSensors) }} sensor(es)
                                        @else
                                            | <i class="fas fa-cloud-off"></i> Firebase no disponible — los sensores se muestran desde BD local
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Temperatura por Sensor</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="temperatureChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Humedad por Sensor</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="humidityChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Sensores Interactiva -->
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card animate__animated animate__fadeInUp">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-list"></i> Estado de Sensores
                                </h4>
                                <div class="d-flex gap-2">
                                    <button id="exportBtn" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-download"></i> Exportar
                                    </button>
                                    <button id="toggleViewBtn" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-th"></i> Vista Tarjetas
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Vista Tabla -->
                                <div id="tableView" class="table-responsive">
                                    <table class="table table-hover table-striped" id="sensorsTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th><i class="fas fa-hashtag"></i> Sensor ID</th>
                                                <th><i class="fas fa-circle"></i> Estado</th>
                                                <th><i class="fas fa-cogs"></i> Tipo</th>
                                                <th><i class="fas fa-map-marker-alt"></i> Área</th>
                                                <th><i class="fas fa-clock"></i> Última Lectura</th>
                                                <th><i class="fas fa-tachometer-alt"></i> Valor 1</th>
                                                <th><i class="fas fa-tachometer-alt"></i> Valor 2</th>
                                                <th><i class="fas fa-exclamation-triangle"></i> Alertas</th>
                                                <th><i class="fas fa-cogs"></i> Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeSensors as $sensorId)
                                                <tr class="sensor-row" data-tipo="{{ $sensorData[$sensorId]['tipo'] ?? '' }}" data-estado="activo" data-area="{{ $sensorData[$sensorId]['area'] ?? '' }}">
                                                    <td>
                                                        <strong class="text-primary">{{ $sensorId }}</strong>
                                                        <div class="progress mt-1" style="height: 4px;">
                                                            <div class="progress-bar bg-success" style="width: 100%"></div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">Activo</span>
                                                    </td>
                                                    <td>
                                                        @if(isset($sensorData[$sensorId]['tipo']))
                                                            @switch($sensorData[$sensorId]['tipo'])
                                                                @case('movimiento_tierra')
                                                                    <span class="badge badge-warning">
                                                                        <i class="fas fa-mountain"></i> Movimiento Tierra
                                                                    </span>
                                                                    @break
                                                                @case('gases_toxicos')
                                                                    <span class="badge badge-warning">
                                                                        <i class="fas fa-skull-crossbones"></i> Gases Tóxicos
                                                                    </span>
                                                                    @break
                                                                @case('signos_vitales')
                                                                    <span class="badge badge-info">
                                                                        <i class="fas fa-heartbeat"></i> Signos Vitales
                                                                    </span>
                                                                    @break
                                                                @default
                                                                    <span class="badge badge-secondary">Desconocido</span>
                                                            @endswitch
                                                        @else
                                                            <span class="badge badge-secondary">Sin Tipo</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $sensorData[$sensorId]['area'] ?? '-' }}</td>
                                                    <td>
                                                        @if(isset($sensorData[$sensorId]['ultima_lectura']))
                                                            <span class="text-success">
                                                                <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($sensorData[$sensorId]['ultima_lectura'])->format('d/m/Y H:i') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($sensorData[$sensorId]['tipo']))
                                                            @switch($sensorData[$sensorId]['tipo'])
                                                                @case('movimiento_tierra')
                                                                    <span class="text-warning">{{ $sensorData[$sensorId]['movimiento'] ?? '-' }} mm</span>
                                                                    @break
                                                                @case('gases_toxicos')
                                                                    <span class="text-warning">CO: {{ $sensorData[$sensorId]['co'] ?? '-' }} ppm</span>
                                                                    @break
                                                                @case('signos_vitales')
                                                                    <span class="text-info">{{ $sensorData[$sensorId]['frecuencia_cardiaca'] ?? '-' }} bpm</span>
                                                                    @break
                                                                @default
                                                                    -
                                                            @endswitch
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($sensorData[$sensorId]['tipo']))
                                                            @switch($sensorData[$sensorId]['tipo'])
                                                                @case('movimiento_tierra')
                                                                    <span class="text-primary">{{ $sensorData[$sensorId]['aceleracion'] ?? '-' }} g</span>
                                                                    @break
                                                                @case('gases_toxicos')
                                                                    <span class="text-success">O₂: {{ $sensorData[$sensorId]['oxigeno'] ?? '-' }}%</span>
                                                                    @break
                                                                @case('signos_vitales')
                                                                    <span class="text-secondary">{{ $sensorData[$sensorId]['saturacion_oxigeno'] ?? '-' }}%</span>
                                                                    @break
                                                                @default
                                                                    -
                                                            @endswitch
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($sensorData[$sensorId]['alertas']) && count($sensorData[$sensorId]['alertas']) > 0)
                                                            <span class="badge badge-warning">{{ count($sensorData[$sensorId]['alertas']) }}</span>
                                                        @else
                                                            <span class="badge badge-success">0</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('firebase-sensors.show', $sensorId) }}" class="btn btn-sm btn-primary" title="Ver Detalles">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                                                                <button class="btn btn-sm btn-warning" title="Configurar" onclick="configurarSensor('{{ $sensorId }}')">
                                                                    <i class="fas fa-cog"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center py-5">
                                                        <i class="fas fa-sensor fa-3x text-muted mb-3"></i>
                                                        <h5 class="text-muted">No hay sensores activos</h5>
                                                        <p class="text-muted">Los sensores aparecerán aquí cuando estén conectados.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Vista Tarjetas (oculta por defecto) -->
                                <div id="cardView" class="row" style="display: none;">
                                    @forelse($activeSensors as $sensorId)
                                        <div class="col-md-4 mb-4 sensor-card" data-tipo="{{ $sensorData[$sensorId]['tipo'] ?? '' }}" data-estado="activo" data-area="{{ $sensorData[$sensorId]['area'] ?? '' }}">
                                            <div class="card h-100 border-primary">
                                                <div class="card-header bg-primary text-white">
                                                    <h6 class="card-title mb-0">
                                                        <i class="fas fa-sensor"></i> {{ $sensorId }}
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-2">
                                                        <span class="badge badge-success">Activo</span>
                                                        @if(isset($sensorData[$sensorId]['tipo']))
                                                            @switch($sensorData[$sensorId]['tipo'])
                                                                @case('movimiento_tierra')
                                                                    <span class="badge badge-warning ml-1">Movimiento Tierra</span>
                                                                    @break
                                                                @case('gases_toxicos')
                                                                    <span class="badge badge-warning ml-1">Gases Tóxicos</span>
                                                                    @break
                                                                @case('signos_vitales')
                                                                    <span class="badge badge-info ml-1">Signos Vitales</span>
                                                                    @break
                                                            @endswitch
                                                        @endif
                                                    </div>
                                                    <p class="mb-1"><strong>Área:</strong> {{ $sensorData[$sensorId]['area'] ?? '-' }}</p>
                                                    <p class="mb-1"><strong>Última Lectura:</strong>
                                                        @if(isset($sensorData[$sensorId]['ultima_lectura']))
                                                            {{ \Carbon\Carbon::parse($sensorData[$sensorId]['ultima_lectura'])->format('d/m/Y H:i') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="card-footer">
                                                    <a href="{{ route('firebase-sensors.show', $sensorId) }}" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-eye"></i> Ver Detalles
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-5">
                                            <i class="fas fa-sensor fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay sensores activos</h5>
                                        </div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para gráficos
    const sensorData = @json($sensorData);
    const labels = [];
    const temperatures = [];
    const humidities = [];

    Object.keys(sensorData).forEach(sensorId => {
        labels.push(sensorId);
        temperatures.push(sensorData[sensorId].temperatura ?? null);
        humidities.push(sensorData[sensorId].humedad ?? null);
    });

    // Gráfico de temperatura
    const tempCtx = document.getElementById('temperatureChart').getContext('2d');
    new Chart(tempCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Temperatura (°C)',
                data: temperatures,
                backgroundColor: 'rgba(197, 45, 89, 0.6)',
                borderColor: 'rgba(197, 45, 89, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de humedad
    const humCtx = document.getElementById('humidityChart').getContext('2d');
    new Chart(humCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Humedad (%)',
                data: humidities,
                backgroundColor: 'rgba(255, 107, 74, 0.6)',
                borderColor: 'rgba(255, 107, 74, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Funcionalidad de filtros
    const tipoFilter = document.getElementById('tipoFilter');
    const estadoFilter = document.getElementById('estadoFilter');
    const areaFilter = document.getElementById('areaFilter');
    const sensorRows = document.querySelectorAll('.sensor-row');
    const sensorCards = document.querySelectorAll('.sensor-card');

    function filterSensors() {
        const tipoValue = tipoFilter.value;
        const estadoValue = estadoFilter.value;
        const areaValue = areaFilter.value;

        sensorRows.forEach(row => {
            const tipo = row.dataset.tipo;
            const estado = row.dataset.estado;
            const area = row.dataset.area;

            const matchesTipo = !tipoValue || tipo === tipoValue;
            const matchesEstado = !estadoValue || estado === estadoValue;
            const matchesArea = !areaValue || area === areaValue;

            if (matchesTipo && matchesEstado && matchesArea) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        sensorCards.forEach(card => {
            const tipo = card.dataset.tipo;
            const estado = card.dataset.estado;
            const area = card.dataset.area;

            const matchesTipo = !tipoValue || tipo === tipoValue;
            const matchesEstado = !estadoValue || estado === estadoValue;
            const matchesArea = !areaValue || area === areaValue;

            if (matchesTipo && matchesEstado && matchesArea) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    tipoFilter.addEventListener('change', filterSensors);
    estadoFilter.addEventListener('change', filterSensors);
    areaFilter.addEventListener('change', filterSensors);

    // Botón de actualizar
    document.getElementById('refreshBtn').addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
        this.disabled = true;

        setTimeout(() => {
            location.reload();
        }, 1000);
    });

    // Alternar vista tabla/tarjetas
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const toggleViewBtn = document.getElementById('toggleViewBtn');

    toggleViewBtn.addEventListener('click', function() {
        if (tableView.style.display === 'none') {
            tableView.style.display = '';
            cardView.style.display = 'none';
            this.innerHTML = '<i class="fas fa-th"></i> Vista Tarjetas';
        } else {
            tableView.style.display = 'none';
            cardView.style.display = '';
            this.innerHTML = '<i class="fas fa-list"></i> Vista Tabla';
        }
    });

    // Botón de exportar (simulado)
    document.getElementById('exportBtn').addEventListener('click', function() {
        showInfo('Funcionalidad de exportación próximamente disponible.');
    });

    // Función para configurar sensor (placeholder)
    window.configurarSensor = function(sensorId) {
        showInfo(`Configuración para sensor ${sensorId} próximamente disponible.`);
    };

    // ESP32: verificar estado al cargar y cada 15 segundos
    verificarEstadoEsp32();
    setInterval(verificarEstadoEsp32, 15000);
});</script>

<script>
// ESP32 funciones
function verificarEstadoEsp32() {
    fetch('{{ route("api.sensor.esp32.status") }}')
        .then(r => r.json())
        .then(data => actualizarUIEsp32(data))
        .catch(() => actualizarUIEsp32({ connected: false }));
}

function conectarEsp32() {
    const statusEl = document.getElementById('esp32StatusText');
    statusEl.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Conectando...';

    fetch('{{ route("api.sensor.esp32.connect") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(r => r.json())
    .then(data => {
        actualizarUIEsp32(data);
        if (data.connected) {
            showAlert(data.mensaje || 'ESP32 conectado', 'success');
        } else {
            showAlert(data.mensaje || 'ESP32 no responde', 'warning');
        }
    })
    .catch(() => showAlert('Error de red al conectar', 'danger'));
}

function desconectarEsp32() {
    fetch('{{ route("api.sensor.esp32.disconnect") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(r => r.json())
    .then(data => {
        actualizarUIEsp32({ connected: false });
        showAlert('ESP32 desconectado manualmente', 'info');
    })
    .catch(() => showAlert('Error de red', 'danger'));
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

    if (!ip) { showAlert('La IP es requerida', 'warning'); return; }
    if (!mac) { showAlert('La MAC es requerida', 'warning'); return; }

    fetch('{{ route("api.sensor.esp32.config") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ ip, mac })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('Configuración guardada correctamente', 'success');
        } else {
            showAlert('Error al guardar: ' + (data.message || 'desconocido'), 'danger');
        }
    })
    .catch(() => showAlert('Error de red al guardar configuración', 'danger'));
}
</script>
@endpush
@endsection
