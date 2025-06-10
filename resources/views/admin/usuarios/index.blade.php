@extends('adminlte::page')
@include('components.modales.crear-usuario')
@section('title', 'Usuarios')

@section('content_header')
    <h1>Gestión de usuarios</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <button class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btn-toggle-filtros">
                    <i class="fas fa-times me-1"></i> <!-- Ícono de cerrar -->
                    <span class="texto-btn d-none d-md-inline"> Ocultar filtros</span>
                </button>
                <button class="btn btn-success" data-toggle="modal" data-target="#modalCrearUsuario">
                    <i class="fas fa-user-plus me-2"></i> Crear Usuario
                </button>
            </div>
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
               <table class="table table-bordered table-striped nowrap" id="usuariosTable" style="width:100%;">
                <thead class="table-primary">
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Correo</th>
                        <th>Rol asignado</th>
                        <th class="text-center">Status</th>
                    </tr>
                    <tr id="fila-filtros" class="filters" style="visibility: visible;">
                        <th><input type="text" class="form-control form-control-sm" placeholder="Nombre de usuario" /></th>
                        <th><input type="text" class="form-control form-control-sm" placeholder="Cédula de usuario" /></th>
                        <th><input type="text" class="form-control form-control-sm" placeholder="Correo de usuario" /></th>
                        <th>
                            <select class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="super_admin">Super admin</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
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
        let tabla = $('#usuariosTable').DataTable({
            dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
            processing: false,
            serverSide: true,
            fixedHeader: true,
            autoWidth: true,
            ajax: {
                url: '{{ route("usuarios.data") }}',
                data: function (d) {
                    $('.filters th').each(function (i) {
                        const input = $(this).find('input, select');
                        if (input.length) {
                            d['columns[' + i + '][search][value]'] = input.val();
                        }
                    });
                }
            },
                        columns: [
                            { data: 'name', name: 'name' },
                            { data: 'cedula', name: 'cedula' },
                            { data: 'email', name: 'email' },
                            { data: 'roles', name: 'roles.name', orderable: false, searchable: false},
                            { data: 'status', name: 'status'},
            ],
            language: {
               url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });

        $('#usuariosTable thead').on('input change', '.filters input, .filters select', function () {
        tabla.ajax.reload();
        });

        $('#btn-toggle-filtros').on('click', function () {
            const filtros = $('#fila-filtros');
            const visible = filtros.css('visibility') !== 'collapse';

            if (visible) {
                filtros.css('visibility', 'collapse');
                $(this).find('i').removeClass('fa-times').addClass('fa-filter');
                $(this).find('.texto-btn').text(' Mostrar filtros');
            } else {
                filtros.css('visibility', 'visible');
                $(this).find('i').removeClass('fa-filter').addClass('fa-times');
                $(this).find('.texto-btn').text(' Ocultar filtros');
            }

            // Si estás usando FixedHeader:
            if ($.fn.dataTable.FixedHeader) {
                $('#usuariosTable').DataTable().fixedHeader.adjust();
            }
        });

    });

//para cambiar el status del usuario
$(document).on('change', '.toggle-status', function () {
    const checkbox = $(this);
    const userId = checkbox.data('id');
    const nuevoEstado = checkbox.is(':checked');

    $.ajax({
        url: '{{ route("usuarios.update-status") }}',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: userId,
            status: nuevoEstado ? 1 : 0
        },
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: res.message,
                timer: 1500,
                showConfirmButton: false
            });

            // Cambiar el texto del estado dinámicamente
            const label = checkbox.closest('.form-check').siblings('.status-label');
label.text(nuevoEstado ? 'Activo' : 'Inactivo');

// Cambia color del texto dinámicamente
label
    .removeClass('text-success text-danger')
    .addClass(nuevoEstado ? 'text-success' : 'text-danger');

        },
        error: function (xhr) {
    if (xhr.status === 403) {
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: xhr.responseJSON.message,
        });
    } else {
        Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
    }

    checkbox.prop('checked', !nuevoEstado); // Revertir el cambio
}

    });
});


//para crear un nuevo usuario

$(document).ready(function () {
    $('#formCrearUsuarioAjax').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let url = "{{ route('usuarios.store') }}";
        let token = $('input[name="_token"]').val();
        let emailInput = form.find('input[name="email"]');
        let email = emailInput.val();
        let boton = form.find('button[type="submit"]');

        // Limpiar errores anteriores y desactivar botón
        boton.prop('disabled', true);
        emailInput.removeClass('is-invalid');

        // Mostrar mensaje de procesamiento
        Swal.fire({
            title: 'Procesando...',
            text: 'Enviando invitación al correo electrónico...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: token,
                email: email
            },
            success: function (response) {
                Swal.close();
                document.activeElement.blur();
                $('#modalCrearUsuario').modal('hide');
                form[0].reset();

                // Recargar la tabla si existe
                if ($('#usuariosTable').length) {
                    $('#usuariosTable').DataTable().ajax.reload();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Usuario creado',
                    text: response.message || 'La invitación fue enviada.',
                    confirmButtonColor: '#3085d6'
                });
            },
            error: function (xhr) {
                Swal.close();
                let msg = 'Ocurrió un error inesperado.';

                if (xhr.status === 422 && xhr.responseJSON.errors.email) {
                    msg = xhr.responseJSON.errors.email[0];
                    emailInput.addClass('is-invalid').focus(); // Enfocar y marcar como inválido
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error al crear el usuario',
                    text: msg,
                    confirmButtonColor: '#d33'
                });

                boton.prop('disabled', false);
            }
        });
    });
});
</script>
@stop
