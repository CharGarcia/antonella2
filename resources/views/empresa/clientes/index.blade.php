@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Gestión de Clientes</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <button class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btn-toggle-filtros">
                    <i class="fas fa-filter me-1"></i>
                    <span class="texto-btn d-none d-md-inline">Mostrar filtros</span>
                </button>
                <button class="btn btn-success" id="btn-nuevo-cliente">
                    <i class="fas fa-plus"></i> Nuevo Cliente
                </button>
            </div>

            <div class="table-responsive" style="max-height: 600px;">
                <table class="table table-bordered table-striped nowrap" id="tabla-clientes" style="width:100%;">
                    <thead class="table-primary">
                        <tr>
                            <th>Nombre</th>
                            <th>Identificación</th>
                            <th>Teléfono</th>
                            <th>Email(s)</th>
                            <th>Dirección</th>
                            <th>Vendedor</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        <tr id="fila-filtros" class="filters">
                            <th><input type="text" class="form-control form-control-sm" placeholder="Nombre"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Identificación"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Teléfono"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Email"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Dirección"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Vendedor"></th>
                            <th>
                                <select class="form-control form-control-sm">
                                    <option value="">Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-cliente" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="modalClienteLabel" aria-hidden="true">
       <div class="modal-dialog modal-lg" role="document">
        <form id="form-cliente">
            @csrf
            <input type="hidden" name="cliente_id" id="cliente_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClienteLabel">Nuevo Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                  <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body row">
                    <div class="form-group col-md-4">
                        <label>Tipo de Identificación</label>
                        <select name="tipo_identificacion" id="tipo_identificacion" class="form-control">
                            <option value="05">Cédula</option>
                            <option value="04">RUC</option>
                            <option value="06">Pasaporte</option>
                            <option value="07">Consumidor final</option>
                            <option value="08">Identificación del exterior</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Número Identificación</label>
                        <input type="text" name="numero_identificacion" id="numero_identificacion" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Razón social / Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" maxlength="10">
                    </div>
                    <div class="form-group col-md-9">
                        <label>Email(s)</label>
                        <input type="text" name="email" id="email" class="form-control" placeholder="Separar múltiples emails con coma">
                    </div>
                    <div class="form-group col-md-12">
                        <label>Dirección</label>
                        <input type="text" name="direccion" id="direccion" class="form-control">
                    </div>
                    <div class="form-group col-md-8">
                        <label>Vendedor</label>
                        <select name="id_vendedor" id="id_vendedor" class="form-control"></select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Plazo crédito (días)</label>
                        <input type="number" name="plazo_credito" id="plazo_credito" class="form-control" value="0">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Provincia</label>
                        <input type="text" name="provincia" id="provincia" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Ciudad</label>
                        <input type="text" name="ciudad" id="ciudad" class="form-control">
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
    $(document).ready(function () {
    $('#telefono').inputmask('0999999999');
    });
$(function () {
    let tabla = $('#tabla-clientes').DataTable({
        dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
        processing: true,
        serverSide: true,
        fixedHeader: true,
        autoWidth: true,
        ajax: {
            url: '{{ route("clientes.data") }}',
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
            { data: 'vendedor.nombre', defaultContent: '-' },
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

    $('#btn-nuevo-cliente').on('click', function () {
        $('#form-cliente')[0].reset();
        $('#cliente_id').val('');
        $('#modalClienteLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo Cliente');
        $('#modal-cliente').modal('show');
    });
});


//para consultar informacion del sri
$('#numero_identificacion').on('change', function () {
    const numero_identificacion = $(this).val();
    const tipo_identificacion = $('#tipo_identificacion').val();

    const esCedulaValida = tipo_identificacion === '05' && numero_identificacion.length === 10;
    const esRucValido = tipo_identificacion === '04' && numero_identificacion.length === 13;

    if (esCedulaValida || esRucValido) {
        Swal.fire({
            title: 'Consultando desde SRI...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: 'http://137.184.159.242:4000/api/sri-identification',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({ identification: numero_identificacion }),
            success: function (response) {
                Swal.close();


            if (esRucValido) {
                const contribuyente = response.data?.datosContribuyente?.[0];
                const establecimientos = response.data?.establecimientos ?? [];
                if (contribuyente) {
                    $('#nombre').val(contribuyente.razonSocial ?? '');
                    $('#estado').val(contribuyente.estadoContribuyenteRuc === 'ACTIVO' ? '1' : '0');
                }
                const matriz = establecimientos.find(est => est.matriz === 'SI');
                if (matriz?.direccionCompleta) {
                const partes = matriz.direccionCompleta.split(' / ');

                $('#provincia').val(partes[0] ?? '');
                $('#ciudad').val(partes[1] ?? '');
                $('#direccion').val(partes[3] ?? '');
            }

            }

             if (esCedulaValida) {
                const contribuyente = response.data;
                $('#nombre').val(contribuyente.nombreCompleto ?? '');
            }

            },
            error: function (xhr) {
                Swal.close();
                Swal.fire('Error', 'No se pudo obtener información del SRI', 'error');
                console.log(xhr.responseText);
            }
        });
    }
});

</script>
@stop
