@extends('layouts.app')
@section('title', isset($area) ? 'Editar Área' : 'Nueva Área')

@section('content')
<style>
    .form-group .form-control {
        background-color: rgba(255, 255, 255, 0.95) !important;
        color: #333 !important;
        border: 1px solid #ddd !important;
    }
    .form-group .form-control:focus {
        background-color: #fff !important;
        color: #333 !important;
        border-color: var(--porco-wine) !important;
        box-shadow: 0 0 10px rgba(80, 200, 120, 0.3) !important;
    }
    .form-group .form-control::placeholder {
        color: #999 !important;
    }
</style>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">{{ isset($area) ? 'Editar Área' : 'Crear Nueva Área' }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ isset($area) ? route('areas.update', $area) : route('areas.store') }}" method="POST">
                    @csrf
                    @if(isset($area)) @method('PUT') @endif

                    <div class="form-group">
                        <label class="bmd-label-floating">Nombre del Área *</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $area->nombre ?? '') }}" required placeholder="Ej: Operaciones, Seguridad">
                    </div>

                    <div class="form-group">
                        <label class="bmd-label-floating">Nivel (opcional)</label>
                        <input type="text" name="nivel" class="form-control" value="{{ old('nivel', $area->nivel ?? '') }}" placeholder="Ej: Nivel 1, Subterráneo">
                    </div>

                    <div class="text-right">
                        <a href="{{ route('areas.index') }}" class="btn btn-default">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            {{ isset($area) ? 'Actualizar' : 'Crear Área' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection