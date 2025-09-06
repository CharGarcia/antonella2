@extends('adminlte::page')

@section('title', 'Gestión de Menús')

@section('content_header')
    <h1>Gestión de Menús</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <button class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btn-toggle-filtros">
                <i class="fas fa-filter me-1"></i>
                <span class="texto-btn d-none d-md-inline">Mostrar filtros</span>
            </button>

            <button class="btn btn-success" id="btn-nuevo-menu">
                <i class="fas fa-plus"></i> Nuevo Menú
            </button>
        </div>

        <div class="table-responsive" style="max-height: 600px;">
            <table class="table table-bordered table-striped nowrap" id="tabla-menus" style="width:100%;">
                <thead class="table-primary">
                    <tr>
                        <th>Nombre del menú</th>
                        <th>Ícono</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    <tr id="fila-filtros" class="filters">
                        <th><input type="text" class="form-control form-control-sm" placeholder="Nombre"></th>
                           <th><input type="text" class="form-control form-control-sm" placeholder="Ícono"></th>
                        <th><input type="text" class="form-control form-control-sm" placeholder="Orden"></th>
                        <th>
                            <select class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Modal Menú --}}
<div class="modal fade" id="modalMenu" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <form id="form-menu">
            @csrf
            <input type="hidden" name="id" id="menu_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMenuLabel">Nuevo Menú</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="form-group col-md-12">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="icono">Ícono</label>
                        <input type="text" name="icono" id="icono" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="orden">Orden</label>
                        <input type="number" name="orden" id="orden" class="form-control" value="0">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
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
$(function () {
    let tabla = $('#tabla-menus').DataTable({
        dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
        processing: false,
        serverSide: true,
        autoWidth: true,
        ajax: {
            url: '{{ route("menus.data") }}',
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
            { data: 'nombre' },
            { data: 'icono' },
            { data: 'orden' },
            { data: 'estado'},
            { data: 'acciones', orderable: false, searchable: false }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        }
    });

    $('#fila-filtros input, #fila-filtros select').on('input change', function () {
    tabla.ajax.reload();
});


    $('#btn-toggle-filtros').on('click', function () {
        const filtros = $('#fila-filtros');
        const visible = filtros.css('visibility') !== 'collapse';

        filtros.css('visibility', visible ? 'collapse' : 'visible');
        $(this).find('i').toggleClass('fa-filter fa-times');
        $(this).find('.texto-btn').text(visible ? 'Mostrar filtros' : 'Ocultar filtros');
    });

    $('#btn-nuevo-menu').on('click', function () {
        $('#form-menu')[0].reset();
        $('#menu_id').val('');
        $('#modalMenuLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo Menú');
        $('#modalMenu').modal('show');
    });

     // Guardar (crear o actualizar)
    $('#form-menu').on('submit', function (e) {
        e.preventDefault();
        const id = $('#menu_id').val();
        const url = id ? `/menus/update/${id}` : '{{ route("menus.store") }}';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function (res) {
                $('#modalMenu').modal('hide');
                tabla.ajax.reload();

                Swal.fire({
                    icon: 'success',
                    title: res.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function () {
                Swal.fire('Error', 'No se pudo guardar el menú', 'error');
            }
        });
    });

    // Editar
    $(document).on('click', '.editar-menu', function () {
        const id = $(this).data('id');
        $.get(`/menus/${id}`, function (data) {
            $('#menu_id').val(data.id);
            $('#nombre').val(data.nombre);
            $('#icono').val(data.icono);
            $('#orden').val(data.orden);
            $('#estado').val(data.estado);
            $('#modalMenuLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar Menú');
            $('#modalMenu').modal('show');
        });
    });


$(document).on('click', '.eliminar-menu', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: '¿Actualizar status?',
        text: "Esta acción desactiva el menú.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/menus/${id}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    $('#tabla-menus').DataTable().ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el menú.', 'error');
                }
            });
        }
    });
});

});

</script>
@stop
