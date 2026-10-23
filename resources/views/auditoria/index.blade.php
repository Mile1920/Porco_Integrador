@extends('layouts.app')
@section('title', 'Auditoría y Seguridad')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">
                    <i class="fas fa-shield-alt"></i> Auditoría y Seguridad
                </h4>
                <p class="card-category">Registro de eventos y actividades del sistema</p>
            </div>
            <div class="card-body">
                <form method="GET" class="row mb-3">
                    <div class="col-md-2">
                        <label>Acción</label>
                        <select name="accion" class="form-control" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach($acciones as $a)
                                <option value="{{ $a }}" {{ request('accion') == $a ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Tabla</label>
                        <select name="tabla" class="form-control" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach($tablas as $t)
                                <option value="{{ $t }}" {{ request('tabla') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Desde</label>
                        <input type="date" name="desde" class="form-control" value="{{ request('desde') }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2">
                        <label>Hasta</label>
                        <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <label>Buscar</label>
                        <div class="input-group">
                            <input type="text" name="busqueda" class="form-control" placeholder="IP o detalle..." value="{{ request('busqueda') }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <a href="{{ route('auditoria.index') }}" class="btn btn-secondary w-100">Limpiar</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Fecha/Hora</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Tabla</th>
                                <th>Registro</th>
                                <th>IP Origen</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->Id_Auditoria }}</td>
                                <td>{{ $log->Fecha ? $log->Fecha->format('d/m/Y H:i:s') : '-' }}</td>
                                <td>{{ $log->usuario?->name ?? 'Sistema' }}</td>
                                <td>
                                    @php
                                        $badge = match($log->Accion) {
                                            'INICIO_SESION', 'CIERRE_SESION' => 'info',
                                            'CREAR', 'CREAR_SENSOR' => 'success',
                                            'ACTUALIZAR' => 'warning',
                                            'ELIMINAR' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">{{ $log->Accion }}</span>
                                </td>
                                <td><code>{{ $log->Tabla_Afectada }}</code></td>
                                <td>{{ $log->Id_Registro ?? '-' }}</td>
                                <td><small>{{ $log->IP_Origen ?? '-' }}</small></td>
                                <td><small>{{ Str::limit($log->Detalle, 60) }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center">No hay registros de auditoría</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div>{{ $logs->total() }} registros</div>
                    <div>{{ $logs->links() }}</div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                @hasrole('administrador-principal')
                    <form method="POST" action="{{ route('auditoria.limpiar') }}" onsubmit="return confirm('¿Eliminar logs anteriores a 3 meses?')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Limpiar logs antiguos
                        </button>
                    </form>
                @endhasrole
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
