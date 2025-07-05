@extends('adminlte::page')

@section('title', 'Vendedores')

@section('content_header')
    <h1>Gestión de Vendedores</h1>
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
                    <button class="btn btn-success" id="btn-nuevo-vendedor">
                        <i class="fas fa-plus"></i> Nuevo Vendedor
                    </button>
                @endif
            </div>
             @if(Permisos::puedeRealizarAccion('ver', $permisos))
            <div class="table-responsive" style="max-height: 600px;">
                <table class="table table-bordered table-striped nowrap" id="tabla-vendedores" style="width:100%;">
                    <thead class="table-primary">
                        <tr>
                            <th>Nombre</th>
                            <th>Identificación</th>
                            <th>Teléfono</th>
                            <th>Email(s)</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        <tr id="fila-filtros" class="filters">
                            <th><input type="text" class="form-control form-control-sm" placeholder="Nombre"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Identificación"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Teléfono"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Email"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Dirección"></th>
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
    @include('empresa.vendedores.partials.modal_vendedor')
@stop

@section('js')
<script>
$(function () {
    let tabla = $('#tabla-vendedores').DataTable({
        dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
        processing: false,
        serverSide: true,
        fixedHeader: true,
        autoWidth: true,
        ajax: {
            url: '{{ route("vendedores.data") }}',
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
            { data: 'numero_identificacion' },
            { data: 'telefono' },
            { data: 'email' },
            { data: 'direccion' },
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
