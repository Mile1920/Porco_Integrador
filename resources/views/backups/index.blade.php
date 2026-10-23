@extends('layouts.app')
@section('title', 'Copias de Seguridad')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title"><i class="fas fa-database"></i> Copias de Seguridad</h4>
                    <p class="card-category">Administración de backups de la base de datos</p>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="crearBackup()" class="btn btn-success btn-sm">
                        <i class="fas fa-plus-circle"></i> Backup Manual
                    </button>
                    <button onclick="actualizarLista()" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sync-alt"></i> Refrescar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info py-2">
                    <i class="fas fa-info-circle"></i>
                    Backup automático cada 48 horas (02:00). Se mantienen solo los 10 backups más recientes.
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="backupsTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Archivo</th>
                                <th>Tamaño</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="backupsBody">
                            <tr><td colspan="4" class="text-center">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function actualizarLista() {
    const tbody = document.getElementById('backupsBody');
    mostrarEstadoBackups(tbody, 'Cargando...');

    fetch('{{ route("backup.list") }}')
        .then(r => r.json())
        .then(data => {
            if (!data.success || !Array.isArray(data.backups) || !data.backups.length) {
                mostrarEstadoBackups(tbody, 'No hay backups disponibles', 'text-muted');
                return;
            }
            const fragment = document.createDocumentFragment();
            data.backups.forEach(function(backup) {
                const row = document.createElement('tr');
                const fileCell = document.createElement('td');
                const code = document.createElement('code');
                code.textContent = backup.file || '';
                fileCell.appendChild(code);
                row.appendChild(fileCell);
                [backup.size_formatted, backup.date].forEach(function(value) {
                    const cell = document.createElement('td');
                    cell.textContent = value || '—';
                    row.appendChild(cell);
                });
                const actions = document.createElement('td');
                const download = document.createElement('a');
                download.href = '{{ url('/api/backups/download') }}/' + encodeURIComponent(backup.file || '');
                download.className = 'btn btn-sm btn-primary';
                download.title = 'Descargar';
                const downloadIcon = document.createElement('i');
                downloadIcon.className = 'fas fa-download';
                download.appendChild(downloadIcon);
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'btn btn-sm btn-danger';
                remove.title = 'Eliminar';
                const removeIcon = document.createElement('i');
                removeIcon.className = 'fas fa-trash';
                remove.appendChild(removeIcon);
                remove.addEventListener('click', () => eliminarBackup(backup.file || ''));
                actions.append(download, remove);
                row.appendChild(actions);
                fragment.appendChild(row);
            });
            tbody.replaceChildren(fragment);
        })
        .catch(() => {
            mostrarEstadoBackups(tbody, 'Error al cargar backups', 'text-danger');
        });
}

function mostrarEstadoBackups(tbody, mensaje, clase = '') {
    const row = document.createElement('tr');
    const cell = document.createElement('td');
    cell.colSpan = 4;
    cell.className = 'text-center ' + clase;
    cell.textContent = mensaje;
    row.appendChild(cell);
    tbody.replaceChildren(row);
}

function crearBackup() {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';

    fetch('{{ route("backup.create") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Backup creado exitosamente');
                actualizarLista();
            } else {
                alert('Error: ' + (data.message || 'desconocido'));
            }
        })
        .catch(() => alert('Error de red'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus-circle"></i> Backup Manual';
        });
}

function eliminarBackup(filename) {
    if (!confirm('¿Eliminar backup ' + filename + '?')) return;

    fetch('{{ url("api/backups") }}/' + encodeURIComponent(filename), { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                actualizarLista();
            } else {
                alert('Error: ' + (data.message || 'desconocido'));
            }
        })
        .catch(() => alert('Error de red'));
}

actualizarLista();
</script>
@endpush
@endsection
