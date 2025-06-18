<div class="d-flex justify-content-end mb-2">
    <button type="button" class="btn btn-sm btn-outline-success mr-2" id="btn-marcar-todos">
        <i class="fas fa-check-square"></i> Marcar todos
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger" id="btn-desmarcar-todos">
        <i class="far fa-square"></i> Desmarcar todos
    </button>
</div>
<div class="mb-3">
    <input type="text" id="buscador-permisos" class="form-control" placeholder="Buscar módulo...">
</div>

<div class="accordion" id="acordeon-permisos">
    @foreach($submenus as $menu => $items)
        <div class="card mb-2">
            <div class="card-header p-2" id="heading-{{ Str::slug($menu) }}">
                <h2 class="mb-0">
                    <button class="btn btn-link text-left w-100" type="button" data-toggle="collapse" data-target="#collapse-{{ Str::slug($menu) }}" aria-expanded="false" aria-controls="collapse-{{ Str::slug($menu) }}">
                        {{ strtoupper($menu) }}
                    </button>
                </h2>
            </div>

            <div id="collapse-{{ Str::slug($menu) }}" class="collapse show" aria-labelledby="heading-{{ Str::slug($menu) }}" data-parent="#acordeon-permisos">
                <div class="card-body p-2">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%;">Módulo</th>
                                @foreach(['ver', 'crear', 'modificar', 'eliminar'] as $accion)
                                    <th class="text-center">{{ ucfirst($accion) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $modulo)
                                @php
                                    $permiso = $permisosAsignados[$modulo->id] ?? null;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <input type="checkbox" class="check-fila mr-2" data-submenu="{{ $modulo->id }}">
                                            <span>{{ $modulo->nombre }}</span>
                                        </div>
                                    </td>
                                    @foreach(['ver', 'crear', 'modificar', 'eliminar'] as $accion)
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       role="switch"
                                                       name="permisos[{{ $modulo->id }}][{{ $accion }}]"
                                                       {{ optional($permiso)->$accion ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
