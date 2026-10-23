@extends('layouts.app')
@section('title', 'Gestión de Trabajadores')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title mb-0">Trabajadores</h4>
                    <p class="card-category mb-0">Administración completa del personal</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                    @hasanyrole('administrador-principal|administrador-area')
                        <a href="{{ route('trabajadores.index') }}?export=pdf" class="btn btn-outline-secondary" 
                           data-toggle="tooltip" data-placement="top" title="Exportar listado de trabajadores en formato PDF">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </a>
                    @endhasanyrole
                    @hasrole('administrador-principal')
                        <a href="{{ route('trabajadores.create') }}" class="btn btn-success"
                           data-toggle="tooltip" data-placement="top" title="Registrar un nuevo trabajador en el sistema">
                            <img src="{{ asset('img/Minero.png') }}" alt="Registrar" class="icon-img me-1"> Registrar Trabajador
                        </a>
                    @endhasrole
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12">
                        @include('trabajadores.partials.search')
                    </div>
                </div>

                @include('trabajadores.partials.table')
            </div>
        </div>
    </div>

    <!-- El panel de 'Acciones Rápidas' eliminado por duplicación en la UI -->
</div>

@push('scripts')
<script>
function openIngresoModal(trabajadorId, nombre) {
    document.getElementById('ingresoModalLabel').textContent = 'Registrar Ingreso — ' + nombre;
    document.getElementById('ingresoForm').action = '/trabajadores/' + encodeURIComponent(trabajadorId) + '/ingreso';
    $('#ingresoModal').modal('show');
}

function openSalidaModal(trabajadorId, nombre) {
    document.getElementById('salidaModalLabel').textContent = 'Registrar Salida — ' + nombre;
    document.getElementById('salidaForm').action = '/trabajadores/' + encodeURIComponent(trabajadorId) + '/salida';
    $('#salidaModal').modal('show');
}

function openReportModal(trabajadorId, nombre) {
    document.getElementById('reportModalLabel').textContent = 'Reportar Condición — ' + nombre;
    document.getElementById('reportForm').action = '/trabajadores/' + encodeURIComponent(trabajadorId) + '/reportar';
    $('#reportModal').modal('show');
}

document.addEventListener('click', function(event) {
    const actionButton = event.target.closest('[data-worker-action]');
    if (actionButton) {
        const action = actionButton.dataset.workerAction;
        const args = [actionButton.dataset.workerId, actionButton.dataset.workerName || ''];
        if (action === 'ingreso') openIngresoModal(...args);
        if (action === 'salida') openSalidaModal(...args);
        return;
    }

    const confirmButton = event.target.closest('.confirm-form-action');
    if (!confirmButton) return;
    event.preventDefault();
    const form = confirmButton.closest('form');
    if (!form) return;
    systemConfirm(confirmButton.dataset.confirmMessage || '¿Confirmar esta acción?')
        .then(confirmed => { if (confirmed) form.requestSubmit(confirmButton); });
});

// Manejar submits de forms en modals
document.getElementById('ingresoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#ingresoModal').modal('hide');
            location.reload(); // Recargar para mostrar mensaje
        } else {
            showError(data.message || 'Error al registrar ingreso');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error al procesar la solicitud');
    });
});

document.getElementById('salidaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#salidaModal').modal('hide');
            location.reload(); // Recargar para mostrar mensaje
        } else {
            showError(data.message || 'Error al registrar salida');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error al procesar la solicitud');
    });
});
</script>
@endpush

@endsection

@include('trabajadores.partials.modals')
