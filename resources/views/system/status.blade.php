@extends('layouts.app')
@section('title', 'Estado del Sistema')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">
                    <i class="fas fa-server"></i> Estado del Sistema Mina Porco
                </h4>
                <p class="card-category text-white-90">Monitoreo en tiempo real de todos los servicios</p>
            </div>
            <div class="card-body">

                <!-- Servicios del Sistema -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3"><i class="fas fa-plug"></i> Servicios del Sistema</h5>
                    </div>

                    <!-- Base de Datos -->
                    <div class="col-md-4">
                        <div class="card border-{{ $databaseStatus['status'] === 'success' ? 'success' : 'warning' }}" style="background-color: {{ $databaseStatus['status'] === 'success' ? 'var(--porco-normal)' : 'var(--porco-warning)' }};">
                            <div class="card-body text-center" style="color: #fff;">
                                <i class="fas fa-database fa-2x mb-2" style="color: #fff;"></i>
                                <h5 class="mb-2" style="font-size: 1.2rem; font-weight: 700; color: #ffffff;">🗄️ Base de Datos</h5>
                                <div style="font-size: 1.1rem; font-weight: 600; line-height: 1.5; color: #ffffff;">
                                    {{ $databaseStatus['message'] }}
                                </div>
                                @if(isset($databaseStatus['version']))
                                    <div class="mt-1" style="font-size: 0.8rem; color: rgba(255,255,255,0.9);">{{ Str::limit($databaseStatus['version'], 25) }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="col-md-4">
                        <div class="card border-{{ $mailStatus['status'] === 'success' ? 'success' : 'warning' }}" style="background-color: {{ $mailStatus['status'] === 'success' ? 'var(--porco-normal)' : 'var(--porco-warning)' }};">
                            <div class="card-body text-center" style="color: #fff;">
                                <i class="fas fa-envelope fa-2x mb-2" style="color: #fff;"></i>
                                <h5 class="mb-2" style="font-size: 1.2rem; font-weight: 700; color: #ffffff;">📧 Correo Electrónico</h5>
                                <div style="font-size: 1.1rem; font-weight: 600; line-height: 1.5; color: #ffffff;">
                                    {{ $mailStatus['message'] }}
                                </div>
                                @if(isset($mailStatus['mailer']))
                                    <div class="mt-1" style="font-size: 0.8rem; color: rgba(255,255,255,0.9);">{{ $mailStatus['mailer'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Storage -->
                    <div class="col-md-4">
                        <div class="card border-{{ $storageStatus['status'] === 'success' ? 'success' : 'warning' }}" style="background-color: {{ $storageStatus['status'] === 'success' ? 'var(--porco-normal)' : 'var(--porco-warning)' }};">
                            <div class="card-body text-center" style="color: #fff;">
                                <i class="fas fa-hdd fa-2x mb-2" style="color: #fff;"></i>
                                <h5 class="mb-2" style="font-size: 1.2rem; font-weight: 700; color: #ffffff;">💾 Almacenamiento</h5>
                                <div style="font-size: 1.1rem; font-weight: 600; line-height: 1.5; color: #ffffff;">
                                    {{ $storageStatus['message'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas del Sistema -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3"><i class="fas fa-chart-bar"></i> Estadísticas del Sistema</h5>
                    </div>

                    <div class="col-md-2">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $stats['trabajadores'] }}</h3>
                                <div class="font-weight-bold" style="font-size: 0.9rem;">Trabajadores Totales</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $stats['trabajadores_activos'] }}</h3>
                                <div class="font-weight-bold" style="font-size: 0.9rem;">Trabajadores Activos</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $stats['ingresos_hoy'] }}</h3>
                                <div class="font-weight-bold" style="font-size: 0.9rem;">Ingresos Hoy</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $stats['incidentes_abiertos'] }}</h3>
                                <div class="font-weight-bold" style="font-size: 0.9rem;">Incidentes Abiertos</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $stats['sensores_locales'] }}</h3>
                                <div class="font-weight-bold" style="font-size: 0.9rem;">Sensores Locales</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="card bg-dark text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $stats['datos_sensores_hoy'] }}</h3>
                                <div class="font-weight-bold" style="font-size: 0.9rem;">Datos de Hoy</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado de Backups -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-shield-alt"></i> Estado de Backups</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-{{ $backupStatus['status'] === 'success' ? 'success' : ($backupStatus['status'] === 'warning' ? 'warning' : 'secondary') }}" style="font-size: 1rem;">
                                    <i class="fas fa-{{ $backupStatus['status'] === 'success' ? 'check-circle' : ($backupStatus['status'] === 'warning' ? 'exclamation-triangle' : 'times-circle') }}"></i>
                                    <strong>{{ $backupStatus['message'] }}</strong>
                                </div>

                                <div class="mb-3 p-3 bg-light rounded">
                                    <h6 class="mb-2" style="font-size: 1rem;"><i class="fas fa-folder"></i> Ubicación de Backups</h6>
                                    <p class="mb-1" style="font-size: 0.9rem;"><strong>Directorio:</strong> <code>storage/app/backups/</code></p>
                                    <p class="mb-1" style="font-size: 0.9rem;"><strong>Tipo:</strong> Base de datos PostgreSQL + archivos</p>
                                    <p class="mb-0" style="font-size: 0.9rem;"><strong>Frecuencia:</strong> Manual (requiere intervención del administrador)</p>
                                </div>

                                @if($backupStatus['last_backup'])
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Último Backup:</strong><br>
                                            <small>{{ $backupStatus['last_backup']['file'] }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Fecha:</strong><br>
                                            <small>{{ $backupStatus['last_backup']['date'] }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Tamaño:</strong><br>
                                            <small>{{ number_format($backupStatus['last_backup']['size'] / 1024, 1) }} KB</small>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Total Backups:</strong><br>
                                            <small>{{ $backupStatus['total_backups'] }}</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Estado de Colas -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-tasks"></i> Estado de Colas</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-{{ $queueStatus['status'] === 'success' ? 'success' : ($queueStatus['status'] === 'warning' ? 'warning' : 'secondary') }}">
                                    <i class="fas fa-{{ $queueStatus['status'] === 'success' ? 'check-circle' : ($queueStatus['status'] === 'warning' ? 'exclamation-triangle' : 'times-circle') }}"></i>
                                    {{ $queueStatus['message'] }}
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Trabajos Pendientes:</strong><br>
                                        <span class="badge badge-info">{{ $queueStatus['pending_jobs'] }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Trabajos Fallidos:</strong><br>
                                        <span class="badge badge-{{ $queueStatus['failed_jobs'] > 0 ? 'warning' : 'success' }}">{{ $queueStatus['failed_jobs'] }}</span>
                                    </div>
                                </div>

                                @if($queueStatus['failed_jobs'] > 0)
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="retryFailedJobs()" 
                                                data-toggle="tooltip" data-placement="top" title="Reintentar ejecutar los trabajos que fallaron anteriormente">
                                            <i class="fas fa-redo"></i> Reintentar Trabajos Fallidos
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones del Sistema -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-tools"></i> Acciones del Sistema</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-lg-3 mb-3">
                                        <button class="btn btn-primary btn-block h-100 d-flex flex-column align-items-center justify-content-center" 
                                                onclick="runBackup('full')" 
                                                data-toggle="tooltip" data-placement="top" title="Crear un backup completo de la base de datos y archivos del sistema"
                                                style="min-height: 80px;">
                                            <i class="fas fa-database fa-2x mb-2"></i>
                                            <span style="font-size: 0.9rem; font-weight: 500;">Backup Completo</span>
                                        </button>
                                    </div>
                                    <div class="col-md-6 col-lg-3 mb-3">
                                        <button class="btn btn-info btn-block h-100 d-flex flex-column align-items-center justify-content-center" 
                                                onclick="runBackup('incremental')" 
                                                data-toggle="tooltip" data-placement="top" title="Crear un backup incremental con solo los cambios recientes"
                                                style="min-height: 80px;">
                                            <i class="fas fa-file-archive fa-2x mb-2"></i>
                                            <span style="font-size: 0.9rem; font-weight: 500;">Backup Incremental</span>
                                        </button>
                                    </div>
                                    <div class="col-md-6 col-lg-3 mb-3">
                                        <button class="btn btn-warning btn-block h-100 d-flex flex-column align-items-center justify-content-center" 
                                                onclick="clearCache()" 
                                                data-toggle="tooltip" data-placement="top" title="Limpiar la caché del sistema para mejorar el rendimiento"
                                                style="min-height: 80px;">
                                            <i class="fas fa-broom fa-2x mb-2"></i>
                                            <span style="font-size: 0.9rem; font-weight: 500;">Limpiar Cache</span>
                                        </button>
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
@endsection

@section('scripts')
<script>
function runBackup(type) {
    const action = type === 'full' ? 'backup:database --full' : 'backup:database --incremental';
    const message = type === 'full' ? 'Ejecutando backup completo...' : 'Ejecutando backup incremental...';

    showInfo(message);

    fetch(`/admin/system/backup/${type}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Backup completado exitosamente');
            setTimeout(() => location.reload(), 2000);
        } else {
            showError('Error en backup: ' + data.message);
        }
    })
    .catch(error => {
        showError('Error ejecutando backup');
        console.error(error);
    });
}

function clearCache() {
    showInfo('Limpiando cache del sistema...');

    fetch('/admin/system/clear-cache', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Cache limpiado exitosamente');
        } else {
            showError('Error limpiando cache');
        }
    })
    .catch(error => {
        showError('Error limpiando cache');
        console.error(error);
    });
}

function retryFailedJobs() {
    showInfo('Reintentando trabajos fallidos...');

    fetch('/admin/system/retry-jobs', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(`Se reintentaron ${data.retried} trabajos`);
            setTimeout(() => location.reload(), 2000);
        } else {
            showError('Error reintentando trabajos');
        }
    })
    .catch(error => {
        showError('Error reintentando trabajos');
        console.error(error);
    });
}
</script>
@endsection
