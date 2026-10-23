@extends('layouts.app')
@section('title', isset($cargo) ? 'Editar Cargo' : 'Nuevo Cargo')

@section('content')
<style>
    .form-group .form-control,
    .form-group select {
        background-color: rgba(255, 255, 255, 0.95) !important;
        color: #333 !important;
        border: 1px solid #ddd !important;
    }
    .form-group .form-control:focus,
    .form-group select:focus {
        background-color: #fff !important;
        color: #333 !important;
        border-color: var(--porco-wine) !important;
        box-shadow: 0 0 10px rgba(80, 200, 120, 0.3) !important;
    }
</style>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">
                    {{ isset($cargo) ? 'Editar Cargo' : 'Crear Nuevo Cargo' }}
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ isset($cargo) ? route('cargos.update', $cargo) : route('cargos.store') }}" method="POST">
                    @csrf
                    @if(isset($cargo)) @method('PUT') @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="bmd-label-floating">Nombre del Cargo *</label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $cargo->nombre ?? '') }}" required>
                                @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="bmd-label-floating">Tipo *</label>
                                <select name="tipo" class="form-control" required>
                                    <option value="mina" {{ old('tipo', $cargo->tipo ?? '') == 'mina' ? 'selected' : '' }}>Mina</option>
                                    <option value="mantenimiento" {{ old('tipo', $cargo->tipo ?? '') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                    <option value="administrativo" {{ old('tipo', $cargo->tipo ?? '') == 'administrativo' ? 'selected' : '' }}>Administrativo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="bmd-label-floating">Descripción (opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion', $cargo->descripcion ?? '') }}</textarea>
                    </div>

                    <div class="text-right mt-4">
                        <a href="{{ route('cargos.index') }}" class="btn btn-default btn-round">Cancelar</a>
                        <button type="submit" class="btn btn-primary btn-round">
                            {{ isset($cargo) ? 'Actualizar Cargo' : 'Crear Cargo' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection