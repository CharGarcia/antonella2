@extends('adminlte::page')

@section('title', 'Roles de usuarios')
@section('content')
<div class="card">
    <div
        id="est-header"
        class="d-flex justify-content-between align-items-center border-bottom mb-3 flex-wrap bg-white pb-2">
        <h4 class="text-primary mb-0">
            <i class="fas fa-certificate text-primary me-2"></i>
            Roles de usuarios
        </h4>
        <div class="d-flex gap-2"></div>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto">
            <table
                class="table-bordered table-striped nowrap table"
                id="rolesTable"
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
                            Nombre
                        </th>
                        <th>Cédula</th>
                        <th>Correo</th>
                        <th>Rol asignado</th>
                    </tr>
                    <tr id="fila-filtros" class="filters" style="visibility: visible">
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Nombre de usuario" />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Cédula de usuario" />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Correo de usuario" />
                        </th>
                        <th>
                            <select class="form-control form-control-sm">
                                <option value="">Todos</option>
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol->name }}">
                                        {{ ucfirst($rol->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(function () {
        let tabla = $('#rolesTable').DataTable({
            dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
            processing: false,
            serverSide: true,
            fixedHeader: true,
            autoWidth: true,
            ajax: {
                url: '{{ route('roles.data') }}',
                data: function (d) {
                    $('.filters th').each(function (i) {
                        const input = $(this).find('input, select');
                        if (input.length) {
                            d['columns[' + i + '][search][value]'] = input.val();
                        }
                    });
                },
            },
            columns: [
                { data: 'name', name: 'name' },
                { data: 'cedula', name: 'cedula' },
                { data: 'email', name: 'email' },
                { data: 'roles', name: 'roles.name', orderable: false, searchable: false },
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json',
            },
        });

        $('#rolesTable thead').on('input change', '.filters input, .filters select', function () {
            tabla.ajax.reload();
        });

        //para cambiar el nombre del boton ocultar filtros
        $('#btn-toggle-filtros').on('click', function () {
            const filtros = $('#fila-filtros');
            const visible = filtros.css('visibility') !== 'collapse';
            filtros.css('visibility', visible ? 'collapse' : 'visible');
            $(this)
                .find('.texto-btn')
                .text(visible ? 'Mostrar filtros' : 'Ocultar filtros');
            // Si estás usando FixedHeader:
            if ($.fn.dataTable.FixedHeader) {
                $('#rolesTable').DataTable().fixedHeader.adjust();
            }
        });
    });

    //para cambiar el nuevo rol del usuario
    $(document).on('change', '.select-role', function () {
        var select = $(this);
        var userId = select.data('user-id');
        var newRoleId = select.val();
        var previousRoleId = select.data('previous-role'); // Guardamos el rol original

        $.ajax({
            url: '/roles/assign',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                user_id: userId,
                role_id: newRoleId,
            },
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Actualización realizada',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    // Guardamos el nuevo rol como el anterior, ya que se asignó correctamente
                    select.data('previous-role', newRoleId);
                } else {
                    Swal.fire('Advertencia', response.message, 'warning');
                    select.val(previousRoleId); // Restaurar sin disparar evento
                }
            },
            error: function (xhr) {
                if (xhr.status === 403) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al actualizar',
                        text: xhr.responseJSON.message,
                        showConfirmButton: true,
                    });
                    select.val(xhr.responseJSON.previous_role_id); // Restaurar sin disparar evento
                } else {
                    //Swal.fire('Error', 'No se pudo actualizar el rol', 'error');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al actualizar',
                        text: 'No se pudo actualizar el rol',
                        showConfirmButton: true,
                    });
                    select.val(previousRoleId); // Restaurar sin disparar evento
                }
            },
        });
    });
</script>
@stop
