@extends('adminlte::page')

@section('title', 'Permisos de usuarios')

@section('content_header')
    <h1>Permisos de roles de usuarios</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <button class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btn-toggle-filtros">
                <i class="fas fa-filter me-1"></i>
                <span class="texto-btn d-none d-md-inline">Mostrar filtros</span>
            </button>
            <!-- Botón nuevo asignacion -->
            <button class="btn btn-success" id="btn-nuevo-permiso" data-bs-toggle="modal" data-bs-target="#modalAgregarPermiso">
                <i class="fas fa-plus"></i> Nuevo permiso
            </button>
        </div>
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-bordered table-striped nowrap" id="permisosTable" style="width:100%;">
                <thead class="table-primary">
                    <tr>
                        <th>Rol</th>
                        <th>Permiso</th>
                        <th class="text-center">Acción</th>
                    </tr>
                    <tr id="fila-filtros" class="filters" style="visibility: collapse;">
                         <th>
                            <select class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="super_admin">Super admin</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </th>
                        <th><input type="text" class="form-control form-control-sm" placeholder="Permisos" /></th>
                        <th></th>
                     </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- Modal: Agregar nuevo permiso a roles -->
  <div class="modal fade" id="modalAgregarPermiso" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="modalAgregarPermisoLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <form id="formAgregarPermiso">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarPermisoLabel"> Agregar nuevo permiso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
          <div class="modal-body">

            <div class="mb-3">
                <label for="nombrePermiso" class="form-label">Nombre del permiso</label>
                <input type="text" class="form-control" id="nombrePermiso" name="nombrePermiso" required>
              </div>
              <div class="mb-2">
                <label class="form-label">Asignar a roles:</label>
                @foreach ($roles as $role)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="rol_{{ $role->id }}">
                    <label class="form-check-label" for="rol_{{ $role->id }}">{{ ucfirst($role->name) }}</label>
                </div>
                @endforeach
              </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
@stop

@section('js')
<script>

// Abrir modal para nuevo permiso
$('#btn-nuevo-permiso').on('click', function () {
    $('#modalAgregarPermisoLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo permiso');
    $('#formAgregarPermiso')[0].reset();
    $('#formAgregarPermiso .form-control').removeClass('is-invalid');
    $('#formAgregarPermiso .invalid-feedback').remove();
    $('#modalAgregarPermiso').modal('show');
});

$(function () {
    let tabla = $('#permisosTable').DataTable({
        dom: '<"row"<"col-md-6 text-left">>rt<"row"<"col-md-12 text-center"p>>',
        processing: true,
        serverSide: true,
        fixedHeader: true,
        autoWidth: true,
        ajax: {
                url: '{{ route("permisos.data") }}',
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
            { data: 'rol', name: 'rol' },
            { data: 'permiso', name: 'permiso' },
            { data: 'accion', name: 'accion', orderable: false, searchable: false }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        }
    });

    $('#permisosTable thead').on('input change', '.filters input, .filters select', function () {
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
                $('#rolesTable').DataTable().fixedHeader.adjust();
            }
        });

    // Eliminar permiso con confirmación
    $(document).on('click', '.btn-eliminar-permiso', function () {
        const btn = $(this);
        const roleId = btn.data('role-id');
        const permission = btn.data('permission');

        Swal.fire({
            title: '¿Eliminar permiso?',
            text: `¿Deseas eliminar el permiso "${permission}" del rol?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("permisos.eliminar") }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        role_id: roleId,
                        permission: permission
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        tabla.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'No se pudo eliminar el permiso'
                        });
                    }
                });
            }
        });
    });
});

$(document).ready(function () {
    $('#formAgregarPermiso').on('submit', function (e) {
    e.preventDefault();

    const nombrePermiso = $('#nombrePermiso').val().trim();
    const rolesSeleccionados = $('input[name="roles[]"]:checked');

    if (nombrePermiso === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Nombre requerido',
            text: 'Por favor, ingresa el nombre del permiso.',
        });
        return;
    }

    if (rolesSeleccionados.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Ningún rol seleccionado',
            text: 'Debes seleccionar al menos un rol para asignar el permiso.',
        });
        return;
    }

    // 🔒 Desactivar botón y mostrar cargando...
    const $btnGuardar = $(this).find('button[type="submit"]');
    $btnGuardar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    const data = $(this).serialize();

    $.ajax({
        url: '{{ route("permisos.guardar") }}',
        type: 'POST',
        data: data,
        success: function (response) {
            $('#modalAgregarPermiso').modal('hide');

            Swal.fire({
                icon: 'success',
                title: 'Permiso guardado',
                text: response.message,
                timer: 2000,
                showConfirmButton: false
            });

            $('#formAgregarPermiso')[0].reset();
            $('#permisosTable').DataTable().ajax.reload(null, false);
        },
        error: function (xhr) {
            let errorMsg = xhr.responseJSON?.message || 'Error al guardar el permiso';
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg,
            });
        },
        complete: function () {
            // 🔓 Reactivar botón
            $btnGuardar.prop('disabled', false).html('Guardar');
        }
    });
});

});

</script>
@stop
