
@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
     <h1>Gestión de Productos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                 @if(Permisos::puedeRealizarAccion('ver', $permisos))
                <button class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btn-toggle-filtros">
                    <i class="fas fa-times me-1"></i> <!-- Ícono de cerrar -->
                      <span class="texto-btn d-none d-md-inline"> Ocultar filtros</span>
                </button>
                 @endif

                @if(Permisos::puedeRealizarAccion('crear', $permisos))
                    <button class="btn btn-success" id="btn-nuevo-producto">
                        <i class="fas fa-plus"></i> Nuevo
                    </button>
                @endif
            </div>

                         @if(Permisos::puedeRealizarAccion('ver', $permisos))
            <div class="table-responsive" style="max-height: 600px;">
                <table class="table table-bordered table-striped nowrap" id="tabla-productos" style="width:100%;">
                    <thead class="table-primary">
                        <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Tarifa Iva</th>
                        <th>Precio Base</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                        </tr>
                        <tr id="fila-filtros" class="filters">
    <th><input class="form-control form-control-sm" placeholder="Código"></th>
    <th><input class="form-control form-control-sm" placeholder="Descripción"></th>
    <th>
        <select class="form-control form-control-sm">
            <option value="">Todos</option>
            <option value="Producto">Producto</option>
            <option value="Servicio">Servicio</option>
            <option value="Activo fijo">Activo fijo</option>
            <option value="Kit/combo">Kit/combo</option>
            <option value="Bien no inventariable">Bien no inventariable</option>
        </select>
    </th>
    <th>
    <select class="form-control form-control-sm">
        <option value="">Todos</option>
        @foreach ($tarifaIva as $codigo => $descripcion)
            <option value="{{ $descripcion }}">{{ $descripcion }}</option>
        @endforeach
    </select>
</th>
    <th><input class="form-control form-control-sm" placeholder="Precio Base"></th>
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
{{-- Modal (create/edit) --}}
@include('empresa.productos.partials.modal_producto')
@stop

@section('js')
<script>
$(function () {
    let tabla = $('#tabla-productos').DataTable({
        dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
        processing: false,
        serverSide: true,
        fixedHeader: true,
        autoWidth: true,
        ajax: {
            url: '{{ route("productos.data") }}',
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
            { data: 'codigo', name: 'codigo' },
            { data: 'descripcion', name: 'descripcion' },
            { data: 'tipo', name: 'tipo' },
            { data: 'tarifa_iva', name: 'tarifa_iva' },
            { data: 'precio_base', name: 'precio_base'},
            { data: 'estado', name: 'estado', orderable:false, searchable:false },
            { data: 'acciones', name: 'acciones', orderable:false, searchable:false },
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

        if (visible) {
            filtros.css('visibility', 'collapse');
            $(this).find('i').removeClass('fa-times').addClass('fa-filter');
            $(this).find('.texto-btn').text(' Mostrar filtros');
        } else {
            filtros.css('visibility', 'visible');
            $(this).find('i').removeClass('fa-filter').addClass('fa-times');
            $(this).find('.texto-btn').text(' Ocultar filtros');
        }
    });

});

</script>
@stop
