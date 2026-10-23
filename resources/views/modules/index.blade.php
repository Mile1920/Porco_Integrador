@extends('layouts.app')
@section('title', 'Módulos - Flujo de Negocio')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
            <div>
                <h4 class="card-title mb-0">Módulos del Sistema</h4>
                <p class="card-category mb-0">Navega por los módulos para ejecutar las acciones del flujo de negocio</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header card-header-rose d-flex align-items-center justify-content-between">
                            <strong class="text-white">Trabajadores</strong>
                            <img src="{{ asset('img/Minero.png') }}" alt="" class="icon-img-small" style="width:28px;height:28px;filter:brightness(0)invert(1);">
                        </div>
                        <div class="card-body">
                            <p class="card-text">Registro y administración del personal. Los trabajadores pueden registrar ingreso/salida desde su perfil.</p>
                            <a href="{{ route('trabajadores.index') }}" class="btn btn-rose">Ir a Trabajadores</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                            <strong class="text-white">Áreas</strong>
                            <img src="{{ asset('img/Monitoreo.png') }}" alt="" class="icon-img-small" style="width:28px;height:28px;filter:brightness(0)invert(1);">
                        </div>
                        <div class="card-body">
                            <p class="card-text">Gestión de áreas, niveles y subniveles de la mina.</p>
                            <a href="{{ route('areas.index') }}" class="btn btn-primary">Ir a Áreas</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header card-header-info d-flex align-items-center justify-content-between">
                            <strong class="text-white">Cargos</strong>
                            <img src="{{ asset('img/Minero.png') }}" alt="" class="icon-img-small" style="width:28px;height:28px;filter:brightness(0)invert(1);">
                        </div>
                        <div class="card-body">
                            <p class="card-text">Administrar cargos y permisos del personal minero.</p>
                            <a href="{{ route('cargos.index') }}" class="btn btn-info">Ir a Cargos</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header card-header-warning d-flex align-items-center justify-content-between">
                            <strong class="text-white">Incidentes</strong>
                            <img src="{{ asset('img/Seguridad.png') }}" alt="" class="icon-img-small" style="width:28px;height:28px;filter:brightness(0)invert(1);">
                        </div>
                        <div class="card-body">
                            <p class="card-text">Validar y cerrar incidentes reportados por trabajadores o detectados por sensores.</p>
                            <a href="{{ route('incidentes.index') }}" class="btn btn-warning">Ir a Incidentes</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header card-header-success d-flex align-items-center justify-content-between">
                            <strong class="text-white">Sensores</strong>
                            <img src="{{ asset('img/Monitoreo.png') }}" alt="" class="icon-img-small" style="width:28px;height:28px;filter:brightness(0)invert(1);">
                        </div>
                        <div class="card-body">
                            <p class="card-text">Monitoreo de sensores, dispositivos y recepción de datos.</p>
                            <a href="{{ route('sensors.index') }}" class="btn btn-success">Ver datos</a>
                            <a href="{{ route('sensors.devices.index') }}" class="btn btn-outline-success">Dispositivos</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                            <strong class="text-white">Mina 2D</strong>
                            <img src="{{ asset('img/Monitoreo.png') }}" alt="" class="icon-img-small" style="width:28px;height:28px;filter:brightness(0)invert(1);">
                        </div>
                        <div class="card-body">
                            <p class="card-text">Plano de niveles y sensores en vista rápida 2D.</p>
                            <a href="{{ route('business.flow.mine2d') }}" class="btn btn-primary">Ver mapa</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header card-header-info d-flex align-items-center justify-content-between">
                            <strong class="text-white">Solicitudes</strong>
                            <img src="{{ asset('img/Minero.png') }}" alt="" class="icon-img-small" style="width:28px;height:28px;filter:brightness(0)invert(1);">
                        </div>
                        <div class="card-body">
                            <p class="card-text">Los trabajadores pueden solicitar herramientas desde su perfil.</p>
                            <a href="{{ route('mi.solicitar') }}" class="btn btn-info">Solicitar herramienta</a>
                            <a href="{{ route('tool_requests.index') }}" class="btn btn-outline-info">Ver Solicitudes</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header card-header-success d-flex align-items-center justify-content-between">
                            <strong class="text-white">Estadísticas</strong>
                            <img src="{{ asset('img/Logo.png') }}" alt="" class="icon-img-small" style="width:28px;height:28px;filter:brightness(0)invert(1);">
                        </div>
                        <div class="card-body">
                            <p class="card-text">Reportes gráficos y exportables del sistema.</p>
                            <a href="{{ route('estadisticas.index') }}" class="btn btn-success">Ver Estadísticas</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header card-header-warning d-flex align-items-center justify-content-between">
                            <strong class="text-white">Control Grupal</strong>
                            <img src="{{ asset('img/Monitoreo.png') }}" alt="" class="icon-img-small" style="width:28px;height:28px;filter:brightness(0)invert(1);">
                        </div>
                        <div class="card-body">
                            <p class="card-text">Ingreso y salida grupal de trabajadores por área.</p>
                            <a href="{{ route('control-grupal') }}" class="btn btn-warning">Ir a Control Grupal</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
