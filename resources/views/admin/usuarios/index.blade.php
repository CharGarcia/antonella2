@extends('adminlte::page')
@include('components.modales.crear-usuario')
@section('title', 'Usuarios')

@section('content_header')
    <h1>Gestión de usuarios</h1>
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
                        <th>Reenvio</th>
                        <th>Rol asignado</th>
                        <th class="text-center">Estado</th>
                    </tr>
                    <tr id="fila-filtros" class="filters" style="visibility: visible;">
                        <th><input type="text" class="form-control form-control-sm" placeholder="Nombre de usuario" /></th>
                        <th><input type="text" class="form-control form-control-sm" placeholder="Cédula de usuario" /></th>
                        <th><input type="text" class="form-control form-control-sm" placeholder="Correo de usuario" /></th>
                        <th></th>
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
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
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
                            { data: 'name',   name: 'users.name' },
                            { data: 'cedula', name: 'users.cedula' },
                            { data: 'email',  name: 'users.email' },
                            { data: 'reenvio', orderable: false, searchable: false },
                            { data: 'roles',  name: 'roles.name', searchable: true, orderable: false },
                            { data: 'estado', name: 'users.estado', searchable: true, orderable: false },
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
// Utilidad: aplica color según el valor ("activo" | "inactivo")
function applyEstadoColor($select) {
    const val = ($select.val() || '').toLowerCase();
    // Limpia clases previas
    $select.removeClass('bg-success bg-danger text-white');

    if (val === 'activo') {
        $select.addClass('bg-success text-white');
    } else if (val === 'inactivo') {
        $select.addClass('bg-danger text-white');
    }
}

// Inicializa colores en todos los selects (llámalo tras render de la tabla)
function initEstadoColors(scope) {
    const $scope = scope ? $(scope) : $(document);
    $scope.find('select.select-estado').each(function () {
        applyEstadoColor($(this));
    });
}

// Al cambiar el select, enviamos AJAX y pintamos color
$(document).on('change', '.select-estado', function () {
    const $select = $(this);
    const userId = $select.data('id');
    const nuevoEstado = $select.val();                 // "activo" | "inactivo"
    const anterior = $select.data('prev') || ($select.val() === 'activo' ? 'inactivo' : 'activo');
    $select.data('prev', nuevoEstado);

    // Pinta de inmediato (optimismo UX)
    applyEstadoColor($select);

    $.ajax({
        url: '{{ route("usuarios.update-status") }}',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: userId,
            estado: nuevoEstado
        },
        success: function (res) {
            // Mensaje opcional
            if (window.Swal && res?.message) {
                Swal.fire({
                    icon: 'success',
                    title: res.message,
                    timer: 1200,
                    showConfirmButton: false
                });
            }

            // Actualiza label si existe cerca
            const $label = $select.closest('td, .d-flex, .input-group').find('.estado-label');
            if ($label.length) {
                const esActivo = (nuevoEstado === 'activo');
                $label
                    .text(esActivo ? 'activo' : 'inactivo')
                    .removeClass('text-success text-danger')
                    .addClass(esActivo ? 'text-success' : 'text-danger');
            }
        },
        error: function (xhr) {
            // Revertir visualmente
            $select.val(anterior);
            applyEstadoColor($select);

            // Mensajes de error
            if (xhr.status === 403 && xhr.responseJSON?.message) {
                if (window.Swal) {
                    Swal.fire({ icon: 'warning', title: 'Advertencia', text: xhr.responseJSON.message });
                } else {
                    alert(xhr.responseJSON.message);
                }
            } else {
                if (window.Swal) {
                    Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
                } else {
                    alert('No se pudo actualizar el estado');
                }
            }
        }
    });
});

// Si usas DataTables, pinta después de cada draw:
$(document).on('init.dt draw.dt', function (e, settings) {
    initEstadoColors(settings && settings.nTable ? settings.nTable : null);
});

// Si NO usas DataTables, puedes llamar una vez al cargar:
$(function () {
    initEstadoColors();
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

$(document).on('click', '.reenviar-correo', function () {
    let userId = $(this).data('id');
    let currentEmail = $(this).data('email');

    Swal.fire({
        title: 'Reenviar invitación',
        input: 'email',
        inputLabel: 'Confirma o corrige el correo',
        inputValue: currentEmail,
        showCancelButton: true,
        confirmButtonText: 'Reenviar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        inputValidator: (value) => {
            if (!value || !/^\S+@\S+\.\S+$/.test(value)) {
                return 'Ingresa un correo válido';
            }
        },
        didOpen: () => {
            Swal.getConfirmButton().disabled = false;
        },
        preConfirm: (nuevoEmail) => {
            Swal.showLoading();

            return $.ajax({
                url: '/usuarios/' + userId + '/reenviar-correo',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    email: nuevoEmail
                }
            }).then(function (response) {
                return { ...response, email: nuevoEmail };
            }).catch(function (xhr) {
    Swal.hideLoading();

    let mensaje = 'No se pudo reenviar el correo.';
    if (xhr.responseJSON && xhr.responseJSON.message) {
        mensaje = xhr.responseJSON.message;
    }

    Swal.showValidationMessage(mensaje);
});

        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                icon: 'success',
                title: 'Correo reenviado',
                html: `
                    <p><strong>Correo enviado a:</strong><br>${result.value.email}</p>
                    <p>${result.value.message}</p>
                `,
                confirmButtonText: 'Aceptar'
            });
        }
    });
});

</script>
@stop
