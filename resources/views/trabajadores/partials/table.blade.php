<div class="table-responsive">
    <table class="table table-hover">
        <thead class="text-primary">
            <tr>
                <th style="width:50px;">Foto</th>
                <th>CI</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Celular</th>
                <th>Edad</th>
                <th>Área</th>
                <th>Cargo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trabajadores as $t)
            <tr>
                <td>
                    @if($t->foto_perfil)
                        <img src="{{ asset('storage/'.$t->foto_perfil) }}" alt="Foto" style="width:40px;height:40px;object-fit:cover;border-radius:50%;">
                    @else
                        <img src="{{ asset('img/default-avatar.svg') }}" alt="Sin foto" style="width:40px;height:40px;object-fit:cover;border-radius:50%;">
                    @endif
                </td>
                <td>{{ $t->ci }}</td>
                <td><strong>{{ $t->nombre_completo }}</strong></td>
                <td>{{ $t->email }}</td>
                <td>{{ $t->celular ?? '-' }}</td>
                <td>{{ $t->edad ?? '-' }}</td>
                <td>{{ $t->area->nombre ?? '-' }}</td>
                <td>{{ $t->cargo->nombre ?? '-' }}</td>
                <td>
                    <span class="badge badge-{{ $t->activo ? 'success' : 'secondary' }}">
                        {{ $t->activo ? 'ACTIVO' : 'INACTIVO' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('trabajadores.edit', $t) }}" class="btn btn-warning btn-sm" 
                        data-toggle="tooltip" data-placement="top" title="Editar" @if(!$t->activo) disabled @endif>
                        <img src="{{ asset('img/Logo.png') }}" alt="Editar" class="icon-img">
                    </a>

                    @hasrole('administrador-principal')
                        <form action="{{ route('trabajadores.destroy', $t) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-{{ $t->activo ? 'danger' : 'success' }} btn-sm confirm-form-action"
                                data-toggle="tooltip" data-placement="top" title="{{ $t->activo ? 'Desactivar' : 'Activar' }}"
                                data-confirm-message="{{ $t->activo ? '¿Desactivar a ' . $t->nombre_completo . '?' : '¿Activar a ' . $t->nombre_completo . '?' }}">
                                <img src="{{ asset('img/SeguridadOperativa.png') }}" alt="estado" class="icon-img">
                            </button>
                        </form>
                    @endhasrole

                    <button type="button" class="btn btn-info btn-sm" 
                        data-toggle="tooltip" data-placement="top" title="Registrar ingreso de {{ $t->nombre_completo }}"
                        data-worker-action="ingreso" data-worker-id="{{ $t->id }}" data-worker-name="{{ $t->nombre_completo }}">
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="Ingreso" class="icon-img">
                    </button>

                    <button type="button" class="btn btn-secondary btn-sm" 
                        data-toggle="tooltip" data-placement="top" title="Registrar salida de {{ $t->nombre_completo }}"
                        data-worker-action="salida" data-worker-id="{{ $t->id }}" data-worker-name="{{ $t->nombre_completo }}">
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="Salida" class="icon-img">
                    </button>

                    <form action="{{ route('trabajadores.sos', $t) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm confirm-form-action"
                            data-toggle="tooltip" data-placement="top" title="SOS"
                            data-confirm-message="¿Enviar SOS por {{ $t->nombre_completo }}?">
                            SOS
                        </button>
                    </form>

                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No hay trabajadores registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $trabajadores->links() }}
</div>
