@extends('layouts.app')
@section('title', 'Crear Sensor en Firebase')

@section('content')
@php
    $areas = \App\Models\Area::where('activo', true)->orderBy('nombre')->get();
@endphp
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Crear Nuevo Sensor en Firebase</h4>
                <p class="card-category">Configure un nuevo dispositivo IoT</p>
            </div>
            <div class="card-body">
                <form action="{{ route('firebase-sensors.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ID del Sensor *</label>
                                <input type="text" name="sensor_id" class="form-control" required
                                       placeholder="ej: sensor_movimiento_tierra_3">
                                <small class="text-muted">Identificador único para el sensor</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo de Sensor *</label>
                                <select name="tipo" class="form-control" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="movimiento_tierra">Movimiento de Tierra</option>
                                    <option value="gases_toxicos">Gases Tóxicos</option>
                                    <option value="signos_vitales">Signos Vitales</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Área *</label>
                                <select name="area_id" class="form-control" required>
                                    <option value="">Seleccionar área</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>{{ $area->nombre }} ({{ $area->nivel ?? 'Sin nivel' }})</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">El área ya incluye información de nivel. Las lecturas aparecen cuando el dispositivo real las envía.</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('sensor-dashboard') }}" class="btn btn-default">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Sensor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
