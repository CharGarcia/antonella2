@extends('adminlte::page')
@section('title', 'Gestión de Submenús')
@section('content')
<div class="card">
    <div
        id="est-header"
        class="d-flex justify-content-between align-items-center border-bottom mb-3 flex-wrap bg-white pb-2">
        <h4 class="text-primary mb-0">
            <i class="far fa-check-circle text-primary me-2"></i>
            Gestión de Submenús
        </h4>
        <div class="d-flex gap-2">
            <button class="btn btn-success" id="btn-nuevo-submenu">
                <i class="fas fa-plus"></i>
                Nuevo Submenú
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table
                class="table-bordered table-striped nowrap table"
                id="tabla-submenus"
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
                            Menu
                        </th>
                        <th>Submenu</th>
                        <th>Ruta</th>
                        <th>Icono</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    <tr id="fila-filtros" class="filters">
                        <th>
                            <select
                                id="filtro-menu"
                                class="form-control form-control-sm filtro-select"
                                style="width: 100%">
                                <option value="">Todos</option>
                                @foreach (App\Models\Admin\Menu::orderBy('nombre')->get() as $menu)
                                    <option value="{{ $menu->nombre }}">
                                        {{ $menu->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </th>

                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Buscar..." />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Buscar..." />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Buscar..." />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Buscar..." />
                        </th>
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

{{-- Modal subMenú --}}
<div
    class="modal fade"
    id="modalSubmenu"
    tabindex="-1"
    data-backdrop="static"
    role="dialog"
    aria-labelledby="modalSubmenuLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="form-submenu">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSubmenuLabel">Submenú</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" id="submenu_id" name="submenu_id" />
                    <div class="form-group col-md-12">
                        <label for="menu_id">Menú</label>
                        <select class="form-control" name="menu_id" id="menu_id" required>
                            @foreach (App\Models\Admin\Menu::all() as $menu)
                                <option value="{{ $menu->id }}">{{ $menu->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="nombre">Nombre del submenu</label>
                        <input
                            type="text"
                            class="form-control"
                            name="nombre"
                            id="nombre"
                            required />
                    </div>
                    <div class="form-group col-md-12">
                        <label for="ruta">Ruta</label>
                        <input type="text" class="form-control" name="ruta" id="ruta" />
                    </div>
                    <div class="form-group col-md-12">
                        <label for="icono">Icono</label>
                        <input type="text" class="form-control" name="icono" id="icono" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="orden">Orden</label>
                        <input
                            type="number"
                            class="form-control"
                            name="orden"
                            id="orden"
                            value="0" />
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
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            const tabla = $('#tabla-submenus').DataTable({
                dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
                processing: false,
                serverSide: true,
                autoWidth: true,
                ajax: {
                    url: '{{ route('submenus.data') }}',
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
                    { data: 'nombre_menu', name: 'menus.nombre' },
                    { data: 'nombre' },
                    { data: 'ruta' },
                    { data: 'icono' },
                    { data: 'orden' },
                    { data: 'estado' },
                    { data: 'acciones', orderable: false, searchable: false },
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json',
                },
            });

            $('#tabla-submenus thead').on(
                'input change',
                '.filters input, .filters select',
                function () {
                    tabla.ajax.reload();
                },
            );

            $('#btn-toggle-filtros').on('click', function () {
                const filtros = $('#fila-filtros');
                const visible = filtros.css('visibility') !== 'collapse';
                filtros.css('visibility', visible ? 'collapse' : 'visible');
                $(this)
                    .find('.texto-btn')
                    .text(visible ? 'Mostrar filtros' : 'Ocultar filtros');
            });

            $('#btn-nuevo-submenu').on('click', function () {
                $('#form-submenu')[0].reset();
                $('#submenu_id').val('');
                $('#modalSubmenuLabel').html(
                    '<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo SubMenú',
                );
                $('#modalSubmenu').modal('show');
            });

            $(document).on('submit', '#form-submenu', function (e) {
                e.preventDefault();
                let id = $('#submenu_id').val();
                let url = id ? `/submenus/update/${id}` : `{{ route('submenus.store') }}`;
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#modalSubmenu').modal('hide');
                        tabla.ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                        });

                        //Swal.fire('¡Éxito!', response.message, 'success');
                    },
                    error: function () {
                        Swal.fire('Error', 'No se pudo guardar el submenú', 'error');
                    },
                });
            });

            //editar
            $(document).on('click', '.editar-submenu', function () {
                const id = $(this).data('id');
                $.get(`/submenus/${id}`, function (data) {
                    $('#submenu_id').val(data.id);
                    $('#menu_id').val(data.menu_id);
                    $('#nombre').val(data.nombre);
                    $('#ruta').val(data.ruta);
                    $('#icono').val(data.icono);
                    $('#orden').val(data.orden);
                    $('#estado').val(data.estado);
                    $('#modalSubMenuLabel').html(
                        '<i class="fas fa-edit text-warning mr-2"></i> Editar SubMenú',
                    );
                    $('#modalSubmenu').modal('show');
                });
            });

            //eliminar el submenu o desactivar
            $(document).on('click', '.eliminar-submenu', function () {
                const id = $(this).data('id');

                Swal.fire({
                    title: '¿Actualizar status?',
                    text: 'Esta acción desactiva el submenú.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, desactivar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/submenus/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function (res) {
                                $('#tabla-submenus').DataTable().ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                            },
                            error: function () {
                                Swal.fire('Error', 'No se pudo eliminar el submenú.', 'error');
                            },
                        });
                    }
                });
            });
        });
    </script>
@endsection
