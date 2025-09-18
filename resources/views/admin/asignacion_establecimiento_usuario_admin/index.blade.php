@extends('adminlte::page')
@section('title', 'Asignación establecimiento')
@section('content')
<div class="card">
    <div
        id="est-header"
        class="d-flex justify-content-between align-items-center border-bottom mb-3 flex-wrap bg-white pb-2">
        <h4 class="text-primary mb-0">
            <i class="fas fa-store text-primary me-2"></i>
            Asignar establecimientos y módulos a usuarios
        </h4>
        <div class="d-flex gap-2">
            <button class="btn btn-success" id="btn-nueva-asignacion-admin">
                <i class="fas fa-plus"></i>
                Asignar establecimiento
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table
                class="table-striped nowrap table"
                id="tabla-asignacion-admin"
                style="width: 100%">
                <thead class="table-primary">
                    <tr>
                        <th>
                            <button
                                class="btn btn-outline-primary btn-xs"
                                id="btn-toggle-filtros"
                                title="Filtros">
                                <i class="fas fa-filter"></i>
                            </button>
                            Cédula
                        </th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Establecimiento</th>
                        <th>Módulos</th>
                        <th>Eliminar</th>
                    </tr>
                    <tr id="fila-filtros" class="filters" style="visibility: visible">
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Cédula" />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Usuario" />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Email" />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Establecimiento" />
                        </th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- Modal agregar establecimiento-->
<div
    class="modal fade"
    id="modalAsignacionAdmin"
    data-backdrop="static"
    tabindex="-1"
    role="dialog">
    <div class="modal-dialog modal-md">
        <form id="form-asignacion-admin">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAsignarEstablecimientoAdminLabel">
                        Asignar establecimiento a Usuario
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body row">
                    <div class="form-group col-md-12 mb-2">
                        <label for="user_id">Usuario</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            @foreach (\App\Models\Admin\User::where('estado', 'activo')
                                    ->whereIn(
                                        'id',
                                        \DB::table('usuario_asignado')
                                            ->where('id_admin', auth()->id())
                                            ->pluck('id_user')
                                    )
                                    ->orderBy('name')
                                    ->get()
                                as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-12 mb-2">
                        <label for="establecimiento_id">Establecimiento</label>
                        <select
                            name="establecimiento_id"
                            id="establecimiento_id"
                            class="form-control"
                            required>
                            @foreach (\App\Models\Admin\Establecimiento::whereIn('id', function ($query) {
                                    $query
                                        ->select('establecimiento_id')
                                        ->from('establecimiento_usuario')
                                        ->where('user_id', auth()->id());
                                })
                                    ->orderBy('nombre_comercial')
                                    ->get()
                                as $establecimiento)
                                <option value="{{ $establecimiento->id }}">
                                    {{ $establecimiento->nombre_comercial }} -
                                    {{ $establecimiento->serie }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal agregar modulos-->
<div class="modal fade" id="modalModulos-admin" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <form id="form-modulos-admin">
            @csrf
            <input type="hidden" name="user_id" id="input-user-id" />
            <input type="hidden" name="establecimiento_id" id="input-establecimiento-id" />
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="titulo-modal-permisos">
                        Asignar Módulos a Usuario
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <div id="tabla-permisos-container" class="table-responsive"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar Permisos</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $(function () {
        const tabla = $('#tabla-asignacion-admin').DataTable({
            dom: '<"row"<"col-md-12">>rt<"row"<"col-md-12 text-center"p>>',
            serverSide: true,
            processing: false,
            fixedHeader: true,
            ajax: {
                url: '{{ route('asignacion_establecimiento_usuario_admin.data') }}',
                data: function (d) {
                    $('.filters th').each(function (i) {
                        const input = $(this).find('input');
                        if (input.length) {
                            d['columns[' + i + '][search][value]'] = input.val();
                        }
                    });
                },
            },
            columns: [
                { data: 'cedula', name: 'users.cedula' },
                { data: 'usuario', name: 'users.name' },
                { data: 'email', name: 'users.email' },
                { data: 'establecimiento', name: 'establecimientos.nombre_comercial' },
                { data: 'modulos', orderable: false, searchable: false },
                { data: 'eliminar', orderable: false, searchable: false },
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json',
            },
        });

        // Filtros dinámicos
        $('#fila-filtros input, #fila-filtros select').on('input change', function () {
            tabla.ajax.reload();
        });

        $('#btn-toggle-filtros').on('click', function () {
            const filtros = $('#fila-filtros');
            const visible = filtros.css('visibility') !== 'collapse';
            filtros.css('visibility', visible ? 'collapse' : 'visible');
            $(this)
                .find('.texto-btn')
                .text(visible ? 'Mostrar filtros' : 'Ocultar filtros');
        });

        //para asignar una establecimiento al usuario nuevo
        $('#btn-nueva-asignacion-admin').on('click', function () {
            $('#form-asignacion-admin')[0].reset();
            $('#user_id').val('').trigger('change');
            $('#establecimiento_id').val('').trigger('change');
            $('#modalAsignarEstablecimientoAdminLabel').html(
                '<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo establecimiento a usuario',
            );
            $('#modalAsignacionAdmin').modal('show');
        });

        $('#user_id').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#modalAsignacionAdmin'),
            width: '100%',
            placeholder: 'Seleccione un usuario',
        });

        $('#establecimiento_id').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#modalAsignacionAdmin'),
            width: '100%',
            placeholder: 'Seleccione un establecimiento',
        });

        // Guardar asignación
        $('#form-asignacion-admin').submit(function (e) {
            e.preventDefault();
            $.post(
                '{{ route('asignacion_establecimiento_usuario_admin.asignar') }}',
                $(this).serialize(),
                function (response) {
                    if (response.success) {
                        $('#modalAsignacionAdmin').modal('hide');
                        tabla.ajax.reload();
                        Swal.fire('Guardado', response.message, 'success');
                    }
                },
            ).fail(function (xhr) {
                let mensaje = xhr.responseJSON?.message || 'Error al guardar.';
                Swal.fire('Atención', mensaje, 'warning');
            });
        });

        // Eliminar asignación de la establecimiento
        $(document).on('click', '.eliminar-asignacion-admin', function () {
            const id = $(this).data('id');

            Swal.fire({
                title: '¿Está seguro?',
                text: 'Esta acción eliminará la asignación.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/asignacion_establecimiento_usuario_admin/eliminar/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function (response) {
                            $('#tabla-asignacion-admin').DataTable().ajax.reload(null, false);
                            Swal.fire('Eliminado', response.message, 'success');
                        },
                        error: function () {
                            Swal.fire('Error', 'No se pudo eliminar la asignación.', 'error');
                        },
                    });
                }
            });
        });

        //para asignar permisos al usuario
        $(document).on('click', '.modulos-asignacion-admin', function () {
            const userId = $(this).data('user-id');
            const establecimientoId = $(this).data('establecimiento-id');
            const usuario = $(this).data('user');
            const establecimiento = $(this).data('establecimiento');

            $('#titulo-modal-permisos').html(`
        <i class="fas fa-cogs mr-1"></i>
        Permisos para <strong>${usuario}</strong> en <strong>${establecimiento}</strong>
    `);

            // Establecer valores en los campos ocultos del formulario
            $('#input-user-id').val(userId);
            $('#input-establecimiento-id').val(establecimientoId);

            $('#tabla-permisos-container').html(
                '<div class="text-center text-muted">Cargando módulos...</div>',
            );
            $('#modalModulos-admin').modal('show');

            $.post(
                '{{ route('asignacion_establecimiento_usuario_admin.permisos') }}',
                {
                    _token: '{{ csrf_token() }}',
                    user_id: userId,
                    establecimiento_id: establecimientoId,
                },
                function (html) {
                    $('#tabla-permisos-container').html(html);
                },
            ).fail(function (xhr) {
                const mensaje = xhr.responseText || 'Error al cargar los módulos.';
                $('#tabla-permisos-container').html(
                    '<div class="text-danger">' + mensaje + '</div>',
                );
            });
        });

        // Aplicar perfil guardado
        $(document).on('click', '#btn-aplicar-perfil', function () {
            const perfil = new URLSearchParams(
                localStorage.getItem('ultimo_perfil_permisos-admin'),
            );
            for (const [key, value] of perfil.entries()) {
                const input = $(`[name="${key}"]`);
                if (input.length && input.attr('type') === 'checkbox') {
                    input.prop('checked', true);
                }
            }
            $(this).closest('.alert').remove();
        });

        //Guardar permisos desde el formulario
        $('#form-modulos-admin').submit(function (e) {
            e.preventDefault();
            const datos = $(this).serialize();
            // Guardar los permisos en localStorage
            localStorage.setItem('ultimo_perfil_permisos-admin', datos);

            $.post(
                '/asignacion_establecimiento_usuario_admin/permisos/guardar',
                datos,
                function () {
                    Swal.fire('Guardado', 'Permisos actualizados correctamente.', 'success');
                    $('#modalModulos-admin').modal('hide');
                },
            ).fail(function () {
                Swal.fire('Error', 'No se pudieron guardar los permisos.', 'error');
            });
        });

        //para marcar todos los items de crear, ver, modificar o eliminar
        $(document).on('change', '.check-todos', function () {
            const accion = $(this).data('accion');
            const checkEstado = $(this).is(':checked');

            $(`input[name^="permisos"][name$="[${accion}]"]`).prop('checked', checkEstado);
        });

        // Marcar todos los checkboxes
        $(document).on('click', '#btn-marcar-todos', function () {
            $('#tabla-permisos-container input[type="checkbox"]').prop('checked', true);
        });

        // Desmarcar todos los checkboxes
        $(document).on('click', '#btn-desmarcar-todos', function () {
            $('#tabla-permisos-container input[type="checkbox"]').prop('checked', false);
        });

        //marcar y desmarcar por fila
        $(document).on('change', '.check-fila', function () {
            const submenuId = $(this).data('submenu');
            const estado = $(this).is(':checked');

            $(`input[name^="permisos[${submenuId}]"]`).prop('checked', estado);
        });

        //para buscar los modulos
        $(document).on('input', '#buscador-permisos', function () {
            const filtro = $(this).val().toLowerCase();

            $('#acordeon-permisos .card').each(function () {
                const card = $(this);
                let coincidencias = 0;

                // Recorrer cada fila del submenú
                card.find('tbody tr').each(function () {
                    const fila = $(this);
                    const texto = fila.text().toLowerCase();

                    if (texto.includes(filtro)) {
                        fila.show();
                        coincidencias++;
                    } else {
                        fila.hide();
                    }
                });

                // Mostrar el menú (card) siempre
                card.show();

                // Expandir si hay coincidencias, contraer si no
                if (coincidencias > 0) {
                    card.find('.collapse').addClass('show');
                } else {
                    card.find('.collapse').removeClass('show');
                }
            });
        });
    });
</script>
@stop
