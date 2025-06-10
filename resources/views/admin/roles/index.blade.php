@extends('adminlte::page')

@section('title', 'Roles de usuarios')

@section('content_header')
    <h1>Roles de usuarios</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <button class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btn-toggle-filtros">
                    <i class="fas fa-times me-1"></i> <!-- Ícono de cerrar -->
                    <span class="texto-btn d-none d-md-inline"> Ocultar filtros</span>
                </button>
            </div>
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
               <table class="table table-bordered table-striped nowrap" id="rolesTable" style="width:100%;">
                <thead class="table-primary">
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Correo</th>
                        <th>Rol asignado</th>
                    </tr>
                    <tr id="fila-filtros" class="filters" style="visibility: visible;">
                        <th><input type="text" class="form-control form-control-sm" placeholder="Nombre de usuario" /></th>
                        <th><input type="text" class="form-control form-control-sm" placeholder="Cédula de usuario" /></th>
                        <th><input type="text" class="form-control form-control-sm" placeholder="Correo de usuario" /></th>
                        <th>
                            <select class="form-control form-control-sm">
                                <option value="">Todos</option>
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol->name }}">{{ ucfirst($rol->name) }}</option>
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
                url: '{{ route("roles.data") }}',
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

            ],
            language: {
               url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });

        $('#rolesTable thead').on('input change', '.filters input, .filters select', function () {
            tabla.ajax.reload();
        });

        //para cambiar el nombre del boton ocultar filtros
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

    });


//para cambiar el nuevo rol del usuario
    $(document).on('change', '.select-role', function() {
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
            role_id: newRoleId
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Actualización realizada',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Guardamos el nuevo rol como el anterior, ya que se asignó correctamente
                select.data('previous-role', newRoleId);
            } else {
                Swal.fire('Advertencia', response.message, 'warning');
                select.val(previousRoleId); // Restaurar sin disparar evento
            }


        },
        error: function(xhr) {
            if (xhr.status === 403) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar',
                    text: xhr.responseJSON.message,
                    showConfirmButton: true
                });
                select.val(xhr.responseJSON.previous_role_id); // Restaurar sin disparar evento
            } else {
                //Swal.fire('Error', 'No se pudo actualizar el rol', 'error');
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar',
                    text: 'No se pudo actualizar el rol',
                    showConfirmButton: true
                });
                select.val(previousRoleId); // Restaurar sin disparar evento
            }
        }
    });
});

</script>
@stop
