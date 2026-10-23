@extends('layouts.app')
@section('title', 'Control Grupal')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title mb-0">Control Grupal por Áreas y Turnos</h4>
                    <p class="card-category mb-0">Gestión masiva de ingresos y salidas de trabajadores</p>
                </div>
                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>
            </div>
            <div class="card-body">

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="text-primary">{{ $totalTrabajadores }}</h5>
                                <p class="mb-0">Total Trabajadores</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success">
                            <div class="card-body text-center text-white">
                                <h5>{{ $trabajadoresPresentes }}</h5>
                                <p class="mb-0">Presentes Hoy</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-secondary">
                            <div class="card-body text-center text-white">
                                <h5>{{ $trabajadoresAusentes }}</h5>
                                <p class="mb-0">Ausentes Hoy</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros y Búsqueda -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form method="GET" class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label><i class="fas fa-search"></i> Buscar</label>
                                        <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Nombre o CI" autocomplete="off">
                                    </div>
                                    <div class="col-md-3">
                                        <label><i class="fas fa-building"></i> Área</label>
                                        <select name="area_id" class="form-control form-select">
                                            <option value="">Todas las áreas</option>
                                            @foreach($areas as $area)
                                                <option value="{{ $area->id }}" {{ $areaId == $area->id ? 'selected' : '' }}>
                                                    {{ $area->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label><i class="fas fa-clock"></i> Turno</label>
                                        <select name="turno" class="form-control form-select">
                                            <option value="">Todos los turnos</option>
                                            <option value="mañana" {{ $turno == 'mañana' ? 'selected' : '' }}>Mañana</option>
                                            <option value="tarde" {{ $turno == 'tarde' ? 'selected' : '' }}>Tarde</option>
                                            <option value="noche" {{ $turno == 'noche' ? 'selected' : '' }}>Noche</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label><i class="fas fa-calendar"></i> Fecha</label>
                                        <input type="date" name="fecha" class="form-control" value="{{ $fecha }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary flex-fill" data-toggle="tooltip" data-placement="top" title="Aplicar filtros de búsqueda">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                            <a href="{{ route('control-grupal.index') }}" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Limpiar todos los filtros">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Grupos por Área y Turno -->
                @foreach($grupos as $areaNombre => $turnos)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-building"></i> {{ $areaNombre }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($turnos as $turnoNombre => $trabajadores)
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">
                                            <i class="fas fa-clock"></i>
                                            Turno {{ ucfirst($turnoNombre) }}
                                            <span class="badge badge-info">{{ count($trabajadores) }} trabajadores</span>
                                        </h6>

                                        <!-- Botones de acción grupal -->
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-success btn-sm"
                                                    data-toggle="tooltip" data-placement="top" title="Registrar ingreso grupal para todos los trabajadores seleccionados del turno {{ ucfirst($turnoNombre) }}"
                                                    onclick="seleccionarTrabajadores('ingreso-{{ Str::slug($areaNombre) }}-{{ $turnoNombre }}')">
                                                <i class="fas fa-sign-in-alt"></i> Ingreso Grupal
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm"
                                                    data-toggle="tooltip" data-placement="top" title="Registrar salida grupal para todos los trabajadores seleccionados del turno {{ ucfirst($turnoNombre) }}"
                                                    onclick="seleccionarTrabajadores('salida-{{ Str::slug($areaNombre) }}-{{ $turnoNombre }}')">
                                                <i class="fas fa-sign-out-alt"></i> Salida Grupal
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" class="select-all"
                                                               data-group="ingreso-{{ Str::slug($areaNombre) }}-{{ $turnoNombre }}"
                                                               data-toggle="tooltip" data-placement="top" title="Seleccionar todos los trabajadores de este turno">
                                                    </th>
                                                    <th>Nombre</th>
                                                    <th>Cargo</th>
                                                    <th>Estado</th>
                                                    <th>Último Registro</th>
                                                    <th style="min-width: 200px;">Acciones Manuales</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($trabajadores as $trabajador)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox"
                                                                   class="trabajador-checkbox"
                                                                   data-group="ingreso-{{ Str::slug($areaNombre) }}-{{ $turnoNombre }}"
                                                                   data-trabajador-id="{{ $trabajador->id }}"
                                                                   data-toggle="tooltip" data-placement="top" title="Seleccionar {{ $trabajador->nombre_completo }} para acción grupal">
                                                        </td>
                                                        <td>{{ $trabajador->nombre_completo }}</td>
                                                        <td>{{ $trabajador->cargo->nombre ?? '-' }}</td>
                                                        <td>
                                                            @if($trabajador->estado_actual === 'presente')
                                                                <span class="badge badge-success">Presente</span>
                                                            @else
                                                                <span class="badge badge-secondary">Ausente</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $ultimoRegistro = \App\Models\Ingreso::where('trabajador_id', $trabajador->id)
                                                                    ->whereDate('registrado_en', $fecha)
                                                                    ->latest('registrado_en')
                                                                    ->first();
                                                            @endphp
                                                            @if($ultimoRegistro)
                                                                {{ $ultimoRegistro->tipo === 'ingreso' ? 'Ingreso' : 'Salida' }}:
                                                                {{ \Carbon\Carbon::parse($ultimoRegistro->registrado_en)->format('H:i') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="btn-group-vertical btn-group-sm d-flex flex-column gap-1" role="group" style="min-width: 120px;">
                                                                @if($trabajador->estado_actual !== 'presente')
                                                                    <button type="button" class="btn btn-success btn-sm w-100"
                                                                            data-toggle="tooltip" data-placement="left" title="Marcar como presente a {{ $trabajador->nombre_completo }}"
                                                                            onclick="marcarPresente({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}', '{{ addslashes($areaNombre) }}')">
                                                                        <img src="{{ asset('img/Seguridad.png') }}" alt="✓" class="icon-img-tiny me-1">
                                                                        Presente
                                                                    </button>
                                                                @else
                                                                    <button type="button" class="btn btn-warning btn-sm w-100"
                                                                            data-toggle="tooltip" data-placement="left" title="Marcar como ausente a {{ $trabajador->nombre_completo }}"
                                                                            onclick="marcarAusente({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}', '{{ addslashes($areaNombre) }}')">
                                                                        <img src="{{ asset('img/Monitoreo.png') }}" alt="✗" class="icon-img-tiny me-1">
                                                                        Ausente
                                                                    </button>
                                                                @endif
                                                                <button type="button" class="btn btn-info btn-sm w-100"
                                                                        onclick="verHistorial({{ $trabajador->id }}, '{{ addslashes($trabajador->nombre_completo) }}')"
                                                                        title="Ver Historial del Día">
                                                                    <img src="{{ asset('img/Logo.png') }}" alt="📋" class="icon-img-tiny me-1">
                                                                    Historial
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>

<!-- Modal para Ingreso Grupal -->
<div class="modal fade" id="modalIngresoGrupal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formIngresoGrupal" method="POST" action="{{ route('control-grupal.ingreso') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Ingreso Grupal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Área</label>
                        <select name="area_id" class="form-control form-select" required>
                            <option value="">Seleccionar área</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Observación (opcional)</label>
                        <input type="text" name="observacion" class="form-control" maxlength="200" placeholder="Nota adicional">
                    </div>
                    <div id="trabajadoresSeleccionados">
                        <!-- Los trabajadores seleccionados se mostrarán aquí -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Ingresos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Salida Grupal -->
<div class="modal fade" id="modalSalidaGrupal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formSalidaGrupal" method="POST" action="{{ route('control-grupal.salida') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Salida Grupal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Área</label>
                        <select name="area_id" class="form-control form-select" required>
                            <option value="">Seleccionar área</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Observación (opcional)</label>
                        <input type="text" name="observacion" class="form-control" maxlength="200" placeholder="Nota adicional">
                    </div>
                    <div id="trabajadoresSeleccionadosSalida">
                        <!-- Los trabajadores seleccionados se mostrarán aquí -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Registrar Salidas</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }
});

function seleccionarTrabajadores(actionType) {
    const checkboxes = document.querySelectorAll(`input[data-group="${actionType}"]:checked`);
    const trabajadorIds = Array.from(checkboxes)
        .map(cb => cb.dataset.trabajadorId)
        .filter(id => /^\d+$/.test(id || ''));

    if (trabajadorIds.length === 0) {
        showWarning('Por favor, seleccione al menos un trabajador');
        return;
    }

    if (actionType.startsWith('ingreso-')) {
        mostrarSeleccionGrupal('trabajadoresSeleccionados', 'formIngresoGrupal', trabajadorIds);
        new bootstrap.Modal(document.getElementById('modalIngresoGrupal')).show();
    } else if (actionType.startsWith('salida-')) {
        mostrarSeleccionGrupal('trabajadoresSeleccionadosSalida', 'formSalidaGrupal', trabajadorIds);
        new bootstrap.Modal(document.getElementById('modalSalidaGrupal')).show();
    }
}

function mostrarSeleccionGrupal(contenedorId, formularioId, trabajadorIds) {
    const contenedor = document.getElementById(contenedorId);
    const cantidad = document.createElement('p');
    const strong = document.createElement('strong');
    strong.textContent = String(trabajadorIds.length);
    cantidad.append(strong, document.createTextNode(' trabajadores seleccionados'));
    contenedor.replaceChildren(cantidad);

    const formulario = document.getElementById(formularioId);
    formulario.querySelectorAll('input[name="trabajador_ids[]"]').forEach(input => input.remove());
    trabajadorIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'trabajador_ids[]';
        input.value = id;
        formulario.insertBefore(input, formulario.firstChild);
    });
}

// Select all functionality and manual actions
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.select-all').forEach(function(selectAll) {
        selectAll.addEventListener('change', function() {
            const group = this.dataset.group;
            const checkboxes = document.querySelectorAll(`input[data-group="${group}"]`);
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    });
});

// Función para marcar como presente manualmente
function marcarPresente(trabajadorId, nombre, area) {
    systemConfirm(`¿Marcar como PRESENTE a ${nombre} en el área ${area}?`).then(confirmed => {
        if (!confirmed) return;
        // Crear formulario dinámicamente
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/trabajadores/${trabajadorId}/ingreso`;

        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);

        // Datos del formulario
        const areaInput = document.createElement('input');
        areaInput.type = 'hidden';
        areaInput.name = 'area_id';
        areaInput.value = ''; // Dejar vacío para que se determine automáticamente
        form.appendChild(areaInput);

        const observacionInput = document.createElement('input');
        observacionInput.type = 'hidden';
        observacionInput.name = 'observacion';
        observacionInput.value = `Marcado manualmente presente - ${new Date().toLocaleString()}`;
        form.appendChild(observacionInput);

        // Agregar al body y enviar
        document.body.appendChild(form);
        form.submit();
    });
}

// Función para marcar como ausente manualmente
function marcarAusente(trabajadorId, nombre, area) {
    systemConfirm(`¿Marcar como AUSENTE a ${nombre} en el área ${area}?`).then(confirmed => {
        if (!confirmed) return;
        // Crear formulario dinámicamente
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/trabajadores/${trabajadorId}/salida`;

        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);

        // Datos del formulario
        const areaInput = document.createElement('input');
        areaInput.type = 'hidden';
        areaInput.name = 'area_id';
        areaInput.value = ''; // Dejar vacío para que se determine automáticamente
        form.appendChild(areaInput);

        const observacionInput = document.createElement('input');
        observacionInput.type = 'hidden';
        observacionInput.name = 'observacion';
        observacionInput.value = `Marcado manualmente ausente - ${new Date().toLocaleString()}`;
        form.appendChild(observacionInput);

        // Agregar al body y enviar
        document.body.appendChild(form);
        form.submit();
    });
}

// Función para ver historial del día
function verHistorial(trabajadorId, nombre) {
    // Abrir modal con historial del día
    const modalContent = `
        <div class="modal fade" id="historialModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, var(--porco-wine), var(--porco-raspberry)); color: white;">
                        <h5 class="modal-title">
                            <img src="/img/Minero.png" alt="Historial" style="width: 24px; height: 24px; margin-right: 10px; filter: brightness(0) invert(1);">
                            Historial del Día - ${nombre}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando historial...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

    document.body.insertAdjacentHTML('beforeend', modalContent);

    // Cargar historial
    fetch(`/trabajadores/${trabajadorId}/historial-hoy`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const content = document.createElement('div');
        content.className = 'table-responsive';
        const table = document.createElement('table');
        table.className = 'table table-striped table-sm';
        const thead = document.createElement('thead');
        thead.className = 'table-dark';
        const heading = document.createElement('tr');
        ['Hora', 'Tipo', 'Área', 'Subnivel'].forEach(label => {
            const th = document.createElement('th');
            th.scope = 'col';
            th.textContent = label;
            heading.appendChild(th);
        });
        thead.appendChild(heading);
        const tbody = document.createElement('tbody');
        if (data.historial && data.historial.length > 0) {
            data.historial.forEach(item => {
                const fecha = new Date(item.registrado_en);
                const row = document.createElement('tr');
                const timeCell = document.createElement('td');
                const time = document.createElement('strong');
                time.textContent = Number.isNaN(fecha.getTime()) ? '—' : fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                timeCell.appendChild(time);
                row.appendChild(timeCell);
                const typeCell = document.createElement('td');
                const badge = document.createElement('span');
                badge.className = 'badge ' + (item.tipo === 'ingreso' ? 'bg-success' : 'bg-warning');
                badge.textContent = item.tipo === 'ingreso' ? 'Entrada' : 'Salida';
                typeCell.appendChild(badge);
                row.appendChild(typeCell);
                [item.area?.nombre || '—', item.observacion || '—'].forEach(value => {
                    const cell = document.createElement('td');
                    cell.textContent = value;
                    row.appendChild(cell);
                });
                tbody.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 4;
            cell.className = 'text-center text-muted py-3';
            cell.textContent = 'No hay registros para hoy';
            row.appendChild(cell);
            tbody.appendChild(row);
        }
        table.append(thead, tbody);
        content.appendChild(table);
        document.querySelector('#historialModal .modal-body').replaceChildren(content);
    })
    .catch(error => {
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-danger';
        errorMessage.textContent = 'Error al cargar el historial';
        document.querySelector('#historialModal .modal-body').replaceChildren(errorMessage);
        console.error('Error al cargar historial:', error);
    });

    const modal = new bootstrap.Modal(document.getElementById('historialModal'));
    modal.show();

    // Limpiar modal cuando se cierre
    document.getElementById('historialModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}
</script>
@endsection
