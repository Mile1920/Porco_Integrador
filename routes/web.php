<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\IncidenteController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\ControlGrupalController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;

// ========================================
// PÁGINA PÚBLICA (tu landing hermosa)
// ========================================
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ========================================
// DASHBOARD Y MÓDULOS (solo usuarios logueados)
// ========================================
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard principal
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $esAdminArea = $user->hasRole('administrador-area');
        $areaId = $esAdminArea && $user->trabajador ? $user->trabajador->area_id : null;
        $limite = now()->subHours(8);

        if ($areaId) {
            $trabajadoresActivos = \App\Models\Trabajador::where('activo', true)->where('area_id', $areaId)->count();
            $incidentesAbiertos = \App\Models\Incidente::whereIn('estado', ['pendiente','en_proceso'])->where('area_id', $areaId)->count();
            $sensorRecientes = \App\Models\SensorData::where('created_at', '>=', now()->subDay())->count();
            $pendientesSalida = \App\Models\Ingreso::where('tipo', 'ingreso')
                ->where('registrado_en', '<=', $limite)
                ->whereNotExists(function ($q) {
                    $q->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from('ingresos', 'salidas')
                        ->whereColumn('salidas.trabajador_id', 'ingresos.trabajador_id')
                        ->where('salidas.tipo', 'salida')
                        ->whereRaw('salidas.registrado_en > ingresos.registrado_en');
                })
                ->whereHas('trabajador', fn($q) => $q->where('area_id', $areaId))
                ->count();
            $recentIncidentes = \App\Models\Incidente::with('area')->where('area_id', $areaId)->orderBy('created_at', 'desc')->limit(6)->get();
            $recentTrabajadores = \App\Models\Trabajador::with('area','cargo')->where('area_id', $areaId)->orderBy('created_at', 'desc')->limit(6)->get();
        } else {
            $trabajadoresActivos = \App\Models\Trabajador::where('activo', true)->count();
            $incidentesAbiertos = \App\Models\Incidente::whereIn('estado', ['pendiente','en_proceso'])->count();
            $sensorRecientes = \App\Models\SensorData::where('created_at', '>=', now()->subDay())->count();
            $pendientesSalida = \App\Models\Ingreso::where('tipo', 'ingreso')
                ->where('registrado_en', '<=', $limite)
                ->whereNotExists(function ($q) {
                    $q->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from('ingresos', 'salidas')
                        ->whereColumn('salidas.trabajador_id', 'ingresos.trabajador_id')
                        ->where('salidas.tipo', 'salida')
                        ->whereRaw('salidas.registrado_en > ingresos.registrado_en');
                })
                ->count();
            $recentIncidentes = \App\Models\Incidente::with('area')->orderBy('created_at', 'desc')->limit(6)->get();
            $recentTrabajadores = \App\Models\Trabajador::with('area','cargo')->orderBy('created_at', 'desc')->limit(6)->get();
        }

        // Series simples para sparkline: últimos 12 periodos (horas)
        $labels = [];
        $counts = [];
        for ($i = 11; $i >= 0; $i--) {
            $from = now()->subHours($i+1);
            $to = now()->subHours($i);
            $labels[] = $to->format('H:00');
            $counts[] = \App\Models\SensorData::where('created_at', '>=', $from)->where('created_at', '<', $to)->count();
        }

        return view('dashboard', compact(
            'trabajadoresActivos',
            'incidentesAbiertos',
            'sensorRecientes',
            'pendientesSalida',
            'recentIncidentes',
            'recentTrabajadores',
            'labels',
            'counts'
        ));
    })->name('dashboard');

    // Centro de Alertas
    Route::get('/alerts', [App\Http\Controllers\AlertController::class, 'index'])->name('alerts.index');

    // Vista del flujo de negocio y mapa 2D - Todos los roles
    Route::middleware('role:administrador-principal|administrador-area|tecnico|trabajador')->group(function () {
        Route::get('/business-flow', function () {
            return view('business_flow');
        })->name('business.flow');
        Route::get('/business-flow/mine-2d', function () {
            return view('modules.mine-2d');
        })->name('business.flow.mine2d');
    });

    // Módulo Sensores - ver datos (técnicos y administradores)
    Route::middleware('role:tecnico|administrador-area|administrador-principal')->group(function () {
        Route::get('/sensors', [SensorController::class, 'index'])->name('sensors.index');
        // Sensor devices management (administradores)
        Route::get('/sensors/devices', [App\Http\Controllers\SensorDeviceController::class, 'index'])->name('sensors.devices.index');
        Route::get('/sensors/devices/create', [App\Http\Controllers\SensorDeviceController::class, 'create'])->name('sensors.devices.create');
        Route::post('/sensors/devices', [App\Http\Controllers\SensorDeviceController::class, 'store'])->name('sensors.devices.store');
        Route::get('/sensors/devices/{sensor}/edit', [App\Http\Controllers\SensorDeviceController::class, 'edit'])->name('sensors.devices.edit');
        Route::put('/sensors/devices/{sensor}', [App\Http\Controllers\SensorDeviceController::class, 'update'])->name('sensors.devices.update');
        Route::delete('/sensors/devices/{sensor}', [App\Http\Controllers\SensorDeviceController::class, 'destroy'])->name('sensors.devices.destroy');
    });

    // Módulo Sensores Firebase - datos simulados (solo administradores principales y de área)
    Route::middleware('role:administrador-principal|administrador-area')->group(function () {
        Route::get('/firebase-sensors', [App\Http\Controllers\FirebaseSensorController::class, 'index'])->name('firebase-sensors.index');
        Route::get('/firebase-sensors/create', [App\Http\Controllers\FirebaseSensorController::class, 'create'])->name('firebase-sensors.create');
        Route::post('/firebase-sensors', [App\Http\Controllers\FirebaseSensorController::class, 'store'])->name('firebase-sensors.store');
        Route::get('/firebase-sensors/{sensorId}', [App\Http\Controllers\FirebaseSensorController::class, 'show'])->name('firebase-sensors.show');
        Route::get('/sensor-dashboard', [App\Http\Controllers\FirebaseSensorController::class, 'dashboard'])->name('sensor-dashboard');
        Route::get('/api/firebase-sensors', [App\Http\Controllers\FirebaseSensorController::class, 'apiData'])->name('api.firebase-sensors');
    });

    // Estadísticas avanzadas (solo administradores)
    Route::middleware('role:administrador-principal|administrador-area')->group(function () {
        Route::get('/estadisticas', [App\Http\Controllers\EstadisticasController::class, 'index'])->name('estadisticas.index');
        Route::get('/estadisticas/pdf', [App\Http\Controllers\EstadisticasController::class, 'pdf'])->name('estadisticas.pdf');
    });

    // Reportes generales (placeholder)
    Route::middleware('role:administrador-principal|administrador-area|tecnico')->group(function () {
        Route::get('/reportes', [App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/ingresos', [App\Http\Controllers\ReporteController::class, 'ingresos'])->name('reportes.ingresos');
        Route::get('/reportes/ingresos/pdf', [App\Http\Controllers\ReporteController::class, 'ingresosPDF'])->name('reportes.ingresos.pdf');
        Route::get('/reportes/incidentes', [App\Http\Controllers\ReporteController::class, 'incidentes'])->name('reportes.incidentes');
        Route::get('/reportes/incidentes/pdf', [App\Http\Controllers\ReporteController::class, 'incidentesPDF'])->name('reportes.incidentes.pdf');
        Route::get('/reportes/sensores', [App\Http\Controllers\ReporteController::class, 'sensores'])->name('reportes.sensores');
        Route::get('/reportes/completo', [App\Http\Controllers\ReporteController::class, 'completo'])->name('reportes.completo');
        Route::get('/reportes/completo/pdf', [App\Http\Controllers\ReporteController::class, 'completoPDF'])->name('reportes.completo.pdf');
    });

    // -------------------------------
    // ÁREAS Y CARGOS → administrador-principal y administrador-area
    // -------------------------------
    Route::middleware('role:administrador-principal|administrador-area')->group(function () {
        Route::resource('areas', AreaController::class)->parameters(['areas' => 'area']);
        Route::resource('cargos', CargoController::class)->parameters(['cargos' => 'cargo']);
    });

    // -------------------------------
    // TRABAJADORES → listado accesible a administrador-principal y administrador-area (solo búsqueda/visualización)
    // -------------------------------
    Route::middleware('role:administrador-principal|administrador-area')->group(function () {
        Route::get('/trabajadores', [TrabajadorController::class, 'index'])->name('trabajadores.index');
        Route::get('/trabajadores/clasificacion', [TrabajadorController::class, 'clasificacion'])->name('trabajadores.clasificacion');
    });

    // -------------------------------
    // TRABAJADORES → administración completa para administradores
    // -------------------------------
    Route::middleware('role:administrador-principal|administrador-area')->group(function () {
        // Registro de ingreso/salida y reportes por trabajador (administradores pueden hacerlo en nombre)
        Route::post('trabajadores/{trabajador}/ingreso', [TrabajadorController::class, 'registrarIngreso'])->name('trabajadores.ingreso');
        Route::post('trabajadores/{trabajador}/salida', [TrabajadorController::class, 'registrarSalida'])->name('trabajadores.salida');
        Route::post('trabajadores/{trabajador}/reportar', [TrabajadorController::class, 'reportarCondicion'])->name('trabajadores.reportar');
        Route::post('trabajadores/{trabajador}/sos', [TrabajadorController::class, 'enviarSos'])->name('trabajadores.sos');
        Route::get('trabajadores/{trabajador}/historial-hoy', [TrabajadorController::class, 'historialHoy'])->name('trabajadores.historial.hoy');
    });

    // Solo administrador principal puede crear/editar/eliminar trabajadores
    Route::middleware('role:administrador-principal')->group(function () {
        Route::resource('trabajadores', TrabajadorController::class)->except(['index'])->parameters(['trabajadores' => 'trabajador']);
    });

    // -------------------------------
    // ACCIONES DEL TRABAJADOR AUTENTICADO (registrar su propio ingreso/salida/reportes)
    // -------------------------------
    Route::middleware(['auth', 'role:trabajador'])->group(function () {
        // Mis acciones del trabajador (panel personal)
        Route::get('/mi', [TrabajadorController::class, 'miDashboard'])->name('mi.index')->middleware('role:trabajador');
        Route::get('/mi/reportes', [TrabajadorController::class, 'miReports'])->name('mi.reportes')->middleware('role:trabajador');
        Route::post('/mi/ingreso', [TrabajadorController::class, 'registrarMiIngreso'])->name('mi.ingreso');
        Route::get('/mi/ingreso', [TrabajadorController::class, 'showIngresoForm'])->name('mi.ingreso.form');
        Route::post('/mi/salida', [TrabajadorController::class, 'registrarMiSalida'])->name('mi.salida');
        Route::get('/mi/salida', [TrabajadorController::class, 'showSalidaForm'])->name('mi.salida.form');
        Route::post('/mi/reportar', [TrabajadorController::class, 'reportarMiCondicion'])->name('mi.reportar');
        Route::get('/mi/reportar', [TrabajadorController::class, 'showReportForm'])->name('mi.reportar.form');
        // Solicitudes de herramientas (trabajador)
        Route::get('/mi/solicitar', [App\Http\Controllers\ToolRequestController::class, 'create'])->name('mi.solicitar');
        Route::post('/mi/solicitar', [App\Http\Controllers\ToolRequestController::class, 'store'])->name('mi.solicitar.store');
    });

    // -------------------------------
    // CONTROL GRUPAL
    // -------------------------------
    Route::middleware('role:administrador-principal|administrador-area')->group(function () {
        Route::get('/control-grupal', [ControlGrupalController::class, 'index'])->name('control-grupal.index');
        Route::post('/control-grupal/ingreso', [ControlGrupalController::class, 'registrarIngresoGrupal'])->name('control-grupal.ingreso');
        Route::post('/control-grupal/salida', [ControlGrupalController::class, 'registrarSalidaGrupal'])->name('control-grupal.salida');
    });

    // -------------------------------
    // PERFIL DE USUARIO
    // -------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // -------------------------------
    // CAMBIO DE CONTRASEÑA
    // -------------------------------
    Route::get('/profile/change-password', [PasswordController::class, 'edit'])->name('profile.change-password');
    Route::patch('/profile/change-password', [PasswordController::class, 'update'])->name('profile.change-password.update');

    // -------------------------------
    // ESTADO DEL SISTEMA
    // -------------------------------
    Route::middleware('role:administrador-principal')->group(function () {
        Route::get('/system/status', [SystemController::class, 'status'])->name('system.status');
    });
});

// Endpoint público para recibir datos desde sensores (si se configuran con SECRET)
Route::post('/sensors/data', [SensorController::class, 'receive'])->middleware('throttle:120,1')->name('sensors.data');

// Endpoint ESP32 (público, para que el ESP32 envie lecturas)
Route::post('/api/sensor/esp32', [App\Http\Controllers\Esp32SensorController::class, 'recibir'])->middleware('throttle:120,1')->name('api.sensor.esp32');
Route::post('/api/sensor/esp32/recibir', [App\Http\Controllers\Esp32SensorController::class, 'recibir'])->middleware('throttle:120,1'); // alias
Route::get('/api/sensor/esp32/health', [App\Http\Controllers\Esp32SensorController::class, 'health'])->name('api.sensor.esp32.health');
Route::get('/api/sensor/esp32/status', [App\Http\Controllers\Esp32SensorController::class, 'status'])->name('api.sensor.esp32.status');

// ESP32 management page (authenticated, role: tecnico or admin)
Route::middleware(['auth', 'role:tecnico|administrador-area|administrador-principal'])->group(function () {
    Route::get('/sensors/esp32', [App\Http\Controllers\Esp32SensorController::class, 'index'])->name('sensors.esp32');
    Route::post('/api/sensor/esp32/connect', [App\Http\Controllers\Esp32SensorController::class, 'connect'])->name('api.sensor.esp32.connect');
    Route::post('/api/sensor/esp32/disconnect', [App\Http\Controllers\Esp32SensorController::class, 'disconnect'])->name('api.sensor.esp32.disconnect');
    Route::post('/api/sensor/esp32/config', [App\Http\Controllers\Esp32SensorController::class, 'updateConfig'])->name('api.sensor.esp32.config');
    Route::get('/api/sensor/esp32/config', [App\Http\Controllers\Esp32SensorController::class, 'getConfig'])->name('api.sensor.esp32.config.get');
});

// API Alertas — polling para popup de emergencia (autenticado)
Route::middleware('auth')->group(function () {
    Route::get('/api/alertas/ultimas', [App\Http\Controllers\AlertaController::class, 'ultimas'])->name('api.alertas.ultimas');
    Route::post('/api/alertas/marcar-leidas', [App\Http\Controllers\AlertaController::class, 'marcarLeidas'])->name('api.alertas.marcar-leidas');
});

// Endpoint para actualizar datos desde Firebase (para sincronización)
Route::post('/api/firebase/sensors/update', function (\Illuminate\Http\Request $request) {
    $firebaseService = app(\App\Services\FirebaseService::class);

    $request->validate([
        'sensor_id' => 'required|string',
        'data' => 'required|array',
    ]);

    $success = $firebaseService->receiveSensorData($request->sensor_id, $request->data);

    return response()->json([
        'success' => $success,
        'message' => $success ? 'Datos actualizados correctamente' : 'Error al actualizar datos'
    ]);
})->name('api.firebase.sensors.update');

// Incidentes - para técnicos/administradores
Route::middleware(['auth','role:tecnico|administrador-area|administrador-principal'])->group(function () {
    Route::get('/incidentes', [IncidenteController::class, 'index'])->name('incidentes.index');
    Route::post('/incidentes/{incidente}/update-estado', [IncidenteController::class, 'updateEstado'])->name('incidentes.updateEstado');
    // Tool requests management (admin/tecnico)
    Route::get('/tool-requests', [App\Http\Controllers\ToolRequestController::class, 'index'])->name('tool_requests.index');
    Route::put('/tool-requests/{tool_request}', [App\Http\Controllers\ToolRequestController::class, 'update'])->name('tool_requests.update');
});

// ========================================
// AUTH ROUTES (login, register, etc.)
// ========================================
// ========================================
// RUTAS DE SISTEMA (backup, cache, etc.)
// ========================================
Route::middleware(['auth', 'role:administrador-principal|administrador-area|tecnico'])->group(function () {
    // Backups contienen información sensible: acceso exclusivo al administrador principal.
    Route::middleware('role:administrador-principal')->group(function () {
    Route::get('/backups', [App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
    Route::post('/api/backups/create', [App\Http\Controllers\BackupController::class, 'create'])->name('backup.create');
    Route::get('/api/backups/list', [App\Http\Controllers\BackupController::class, 'list'])->name('backup.list');
    Route::get('/api/backups/download/{filename}', [App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');
    Route::delete('/api/backups/{filename}', [App\Http\Controllers\BackupController::class, 'delete'])->name('backup.delete');
    });

    // Auditoría
    Route::get('/auditoria', [App\Http\Controllers\AuditController::class, 'index'])->name('auditoria.index');
    Route::get('/auditoria/{id}', [App\Http\Controllers\AuditController::class, 'show'])->name('auditoria.show');
});

Route::post('/auditoria/limpiar/antiguos', [App\Http\Controllers\AuditController::class, 'limpiar'])
    ->middleware(['auth', 'role:administrador-principal'])
    ->name('auditoria.limpiar');

Route::middleware(['auth', 'role:administrador-principal'])->prefix('admin/system')->group(function () {
    Route::post('/backup/{type?}', [App\Http\Controllers\BackupController::class, 'create'])->name('system.backup.create');
    Route::get('/backup/list', [App\Http\Controllers\BackupController::class, 'list'])->name('system.backup.list');
    Route::get('/backup/download/{filename}', [App\Http\Controllers\BackupController::class, 'download'])->name('system.backup.download');
    Route::delete('/backup/{filename}', [App\Http\Controllers\BackupController::class, 'delete'])->name('system.backup.delete');
    Route::post('/clear-cache', [App\Http\Controllers\BackupController::class, 'clearCache'])->name('system.clear-cache');
    Route::post('/retry-jobs', [App\Http\Controllers\BackupController::class, 'retryJobs'])->name('system.retry-jobs');
});

require __DIR__.'/auth.php';
