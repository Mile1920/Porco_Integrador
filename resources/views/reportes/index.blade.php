@extends('layouts.app')
@section('title', 'Centro de Reportes')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, var(--porco-wine), var(--porco-raspberry)); color: white; border: none;">
                <div>
                    <h3 class="card-title mb-2" style="font-size: 2rem; font-weight: 700;">
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="Reportes" class="icon-img-small me-3" style="width: 32px; height: 32px; filter: brightness(0) invert(1);">
                        Centro de Reportes
                    </h3>
                    <p class="card-category mb-0" style="font-size: 1.2rem; font-weight: 500; color: rgba(255,255,255,0.9);">Reportes completos y estadísticas del sistema Mina Porco</p>
                </div>
                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>
            </div>
            <div class="card-body">

                <!-- Estadísticas Rápidas -->
                <!-- Estadísticas Rápidas Mejoradas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card" style="background: linear-gradient(135deg, var(--porco-wine), var(--porco-raspberry)); color: white; border: none;">
                            <div class="card-body text-center py-4">
                                <div style="font-size: 3rem; font-weight: 700; margin-bottom: 10px;">{{ $stats['total_trabajadores'] }}</div>
                                <div style="font-size: 1.1rem; font-weight: 600;">Trabajadores Activos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card" style="background: linear-gradient(135deg, var(--porco-info), var(--porco-wine)); color: white; border: none;">
                            <div class="card-body text-center py-4">
                                <div style="font-size: 3rem; font-weight: 700; margin-bottom: 10px;">{{ $stats['ingresos_hoy'] }}</div>
                                <div style="font-size: 1.1rem; font-weight: 600;">Ingresos Hoy</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card" style="background: linear-gradient(135deg, var(--porco-warning), var(--porco-raspberry)); color: white; border: none;">
                            <div class="card-body text-center py-4">
                                <div style="font-size: 3rem; font-weight: 700; margin-bottom: 10px;">{{ $stats['incidentes_pendientes'] }}</div>
                                <div style="font-size: 1.1rem; font-weight: 600;">Incidentes Pendientes</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card" style="background: linear-gradient(135deg, var(--porco-anomaly), #6a1b9a); color: white; border: none;">
                            <div class="card-body text-center py-4">
                                <div style="font-size: 3rem; font-weight: 700; margin-bottom: 10px;">{{ $sensorStats['activos'] }}</div>
                                <div style="font-size: 1.1rem; font-weight: 600;">Sensores Activos</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reportes Disponibles -->
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="mb-4" style="font-size: 1.8rem; font-weight: 700; color: #333; border-bottom: 3px solid var(--porco-wine); padding-bottom: 10px;">
                            <img src="{{ asset('img/Logo.png') }}" alt="Reportes" class="icon-img-small me-3" style="width: 28px; height: 28px;">
                            Reportes Disponibles
                        </h4>
                    </div>

                    <!-- Reporte Completo -->
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100 border-0 shadow-lg" style="background: linear-gradient(135deg, #ffffff, #f8f9fa);">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <img src="{{ asset('img/Monitoreo.png') }}" alt="Completo" style="width: 60px; height: 60px; filter: brightness(0.8);">
                                </div>
                                <h5 class="card-title" style="font-size: 1.3rem; font-weight: 700; color: #333; margin-bottom: 10px;">Reporte Completo</h5>
                                <p style="color: #666; font-size: 0.95rem; line-height: 1.4; margin-bottom: 15px;">Vista general completa de todo el sistema Mina Porco</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('reportes.completo') }}" class="btn btn-primary btn-sm px-3" style="font-weight: 600;">
                                        Ver Online
                                    </a>
                                    <a href="{{ route('reportes.completo.pdf') }}" class="btn btn-danger btn-sm px-3" target="_blank" style="font-weight: 600;">
                                        PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reporte de Ingresos -->
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100 border-0 shadow-lg" style="background: linear-gradient(135deg, #ffffff, #f8f9fa);">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <img src="{{ asset('img/Seguridad.png') }}" alt="Ingresos" style="width: 60px; height: 60px; filter: brightness(0.8);">
                                </div>
                                <h5 class="card-title" style="font-size: 1.3rem; font-weight: 700; color: #333; margin-bottom: 10px;">Reporte de Ingresos</h5>
                                <p style="color: #666; font-size: 0.95rem; line-height: 1.4; margin-bottom: 15px;">Control de acceso y registro de horarios</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('reportes.ingresos') }}" class="btn btn-info btn-sm px-3" style="font-weight: 600;">
                                        Ver Online
                                    </a>
                                    <a href="{{ route('reportes.ingresos.pdf') }}" class="btn btn-danger btn-sm px-3" target="_blank" style="font-weight: 600;">
                                        PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reporte de Incidentes -->
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100 border-0 shadow-lg" style="background: linear-gradient(135deg, #ffffff, #f8f9fa);">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <img src="{{ asset('img/SeguridadOperativa.png') }}" alt="Incidentes" style="width: 60px; height: 60px; filter: brightness(0.8);">
                                </div>
                                <h5 class="card-title" style="font-size: 1.3rem; font-weight: 700; color: #333; margin-bottom: 10px;">Reporte de Incidentes</h5>
                                <p style="color: #666; font-size: 0.95rem; line-height: 1.4; margin-bottom: 15px;">Incidentes reportados y estado de resolución</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('reportes.incidentes') }}" class="btn btn-warning btn-sm px-3" style="font-weight: 600;">
                                        Ver Online
                                    </a>
                                    <a href="{{ route('reportes.incidentes.pdf') }}" class="btn btn-danger btn-sm px-3" target="_blank" style="font-weight: 600;">
                                        PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reporte de Sensores -->
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100 border-0 shadow-lg" style="background: linear-gradient(135deg, #ffffff, #f8f9fa);">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <img src="{{ asset('img/Monitoreo.png') }}" alt="Sensores" style="width: 60px; height: 60px; filter: brightness(0.8);">
                                </div>
                                <h5 class="card-title" style="font-size: 1.3rem; font-weight: 700; color: #333; margin-bottom: 10px;">Reporte de Sensores</h5>
                                <p style="color: #666; font-size: 0.95rem; line-height: 1.4; margin-bottom: 15px;">Datos IoT y estado de dispositivos de monitoreo</p>
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('reportes.sensores') }}" class="btn btn-secondary btn-sm px-3" style="font-weight: 600;">
                                        Ver Online
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reporte de Sensores -->
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100 border-0 shadow-lg" style="background: linear-gradient(135deg, #ffffff, #f8f9fa);">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <img src="{{ asset('img/Monitoreo.png') }}" alt="Sensores" style="width: 60px; height: 60px; filter: brightness(0.8);">
                                </div>
                                <h5 class="card-title" style="font-size: 1.3rem; font-weight: 700; color: #333; margin-bottom: 10px;">Reporte de Sensores</h5>
                                <p style="color: #666; font-size: 0.95rem; line-height: 1.4; margin-bottom: 15px;">Estado de sensores y datos ambientales</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('reportes.sensores') }}" class="btn btn-secondary btn-sm px-3" style="font-weight: 600;">
                                        Ver Online
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Sistema Mejorada -->
                <div class="row mt-5">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, var(--porco-wine), var(--porco-raspberry)); color: white;">
                            <div class="card-header border-0" style="background: transparent;">
                                <h4 class="mb-0" style="font-size: 1.5rem; font-weight: 700;">
                                    <img src="{{ asset('img/Monitoreo.png') }}" alt="Info" class="icon-img-small me-3" style="width: 28px; height: 28px; filter: brightness(0) invert(1);">
                                    Información del Sistema
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.1);">
                                            <div style="font-size: 2.5rem; font-weight: 700; margin-bottom: 5px;">{{ $stats['total_usuarios'] }}</div>
                                            <div style="font-size: 1rem; font-weight: 600;">Usuarios Totales</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.1);">
                                            <div style="font-size: 2.5rem; font-weight: 700; margin-bottom: 5px;">{{ $stats['total_areas'] }}</div>
                                            <div style="font-size: 1rem; font-weight: 600;">Áreas Activas</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.1);">
                                            <div style="font-size: 2.5rem; font-weight: 700; margin-bottom: 5px;">{{ $stats['total_cargos'] }}</div>
                                            <div style="font-size: 1rem; font-weight: 600;">Cargos</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.1);">
                                            <div style="font-size: 2.5rem; font-weight: 700; margin-bottom: 5px;">{{ $stats['incidentes_mes'] }}</div>
                                            <div style="font-size: 1rem; font-weight: 600;">Incidentes del Mes</div>
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
</div>
@endsection
