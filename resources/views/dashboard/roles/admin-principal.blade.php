<!-- Dashboard para Administrador Principal -->
@include('dashboard.partials.stats')

<div class="row mt-4">
    <div class="col-lg-12">
        @include('dashboard.partials.recent_incidentes')
    </div>
</div>

<!-- Solicitudes de Herramientas -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <img src="{{ asset('img/Seguridad.png') }}" alt="Herramientas" class="icon-img-small me-2" style="width: 24px; height: 24px;">
                    Solicitudes de Herramientas Pendientes
                </h4>
                <p class="card-category">Herramientas solicitadas por trabajadores</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-dark-tool-requests">
                        <thead>
                            <tr>
                                <th>Solicitante</th>
                                <th>Herramienta</th>
                                <th>Cantidad</th>
                                <th>Estado</th>
                                <th>Fecha Solicitud</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\ToolRequest::with('trabajador')->where('estado', 'pendiente')->latest()->limit(10)->get() as $solicitud)
                            <tr>
                                <td>{{ $solicitud->trabajador->nombre_completo ?? 'N/A' }}</td>
                                <td>{{ $solicitud->herramienta }}</td>
                                <td>{{ $solicitud->cantidad }}</td>
                                <td>
                                    <span class="badge badge-warning">{{ ucfirst($solicitud->estado) }}</span>
                                </td>
                                <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('tool_requests.index') }}" class="btn btn-sm btn-primary">
                                        Gestionar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay solicitudes pendientes</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('tool_requests.index') }}" class="btn btn-outline-primary">
                        Ver Todas las Solicitudes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <img src="{{ asset('img/Minero.png') }}" alt="Control" class="icon-img-small me-2" style="width: 24px; height: 24px;">
                    Control de Ingreso/Salida de Trabajadores
                </h4>
                <p class="card-category">Historial de entradas y salidas</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead style="background-color: var(--porco-wine); color: #ffffff; border-bottom: 2px solid var(--porco-wine);">
                            <tr>
                                <th style="border: none;">Trabajador</th>
                                <th style="border: none;">Tipo</th>
                                <th style="border: none;">Área</th>
                                <th style="border: none;">Nivel</th>
                                <th style="border: none;">Fecha/Hora</th>
                            </tr>
                        </thead>
                        <tbody style="color: #ffffff;">
                            @forelse(\App\Models\Ingreso::with('trabajador', 'area')->latest()->limit(15)->get() as $ingreso)
                                <tr>
                                    <td style="border: none; color: #ffffff;">{{ $ingreso->trabajador->nombre_completo ?? 'N/A' }}</td>
                                    <td style="border: none;">
                                        <span class="badge {{ $ingreso->tipo === 'ingreso' ? 'badge-success' : 'badge-warning' }}" style="color: #ffffff;">
                                            {{ ucfirst($ingreso->tipo) }}
                                        </span>
                                    </td>
                                    <td style="border: none; color: #ffffff;">{{ $ingreso->area->nombre ?? '-' }}</td>
                                    <td style="border: none; color: #ffffff;">{{ $ingreso->area->nivel ?? '-' }}</td>
                                    <td style="border: none; color: #ffffff;">{{ \Carbon\Carbon::parse($ingreso->registrado_en)->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center" style="color: #ffffff; border: none;">No hay registros recientes</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">Vista informativa del flujo de trabajadores</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('trabajadorSelect').addEventListener('change', function() {
    const form = document.getElementById('ingresoForm');
    const trabajadorId = this.value;
    if (trabajadorId) {
        form.action = form.action.replace('__PLACEHOLDER__', trabajadorId);
    }
});
</script>
