@extends('adminlte::page')

@section('title', 'Lista Precios')

@section('content')
<div class="card">
    <div
        id="est-header"
        class="d-flex justify-content-between align-items-center border-bottom mb-3 flex-wrap bg-white pb-2">
        <h4 class="text-primary mb-0">
            <i class="fas fa-comments-dollar text-primary me-2"></i>
            Gestión de listas de precios
        </h4>
        <div class="d-flex gap-2">
            @if (Permisos::puedeRealizarAccion('crear', $permisos))
                <button class="btn btn-success" id="btn-nuevo-lista-precios">
                    <i class="fas fa-plus"></i>
                    Nueva
                </button>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if (Permisos::puedeRealizarAccion('ver', $permisos))
            <div class="table-responsive" style="max-height: 600px">
                <table
                    class="table-bordered table-striped nowrap table"
                    id="tabla-lista-precios"
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
                            <th>Descripcion</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        <tr id="fila-filtros" class="filters">
                            <th>
                                <input class="form-control form-control-sm" placeholder="Nombre" />
                            </th>
                            <th>
                                <input
                                    class="form-control form-control-sm"
                                    placeholder="Descripcion" />
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
        @endif
    </div>
</div>
@include('empresa.listaprecios.partials.modal_listaprecios')
@stop

@section('js')
<script>
    $(function () {
        let tabla = $('#tabla-lista-precios').DataTable({
            dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
            processing: false,
            serverSide: true,
            fixedHeader: true,
            autoWidth: true,
            ajax: {
                url: '{{ route('lista-precios.data') }}',
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
                { data: 'nombre' },
                { data: 'descripcion' },
                { data: 'estado' },
                { data: 'acciones', orderable: false, searchable: false },
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json',
            },
        });

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
    });
</script>
@stop
