@extends('adminlte::page')

@section('title', 'Establecimientos')

@section('content_header')
    <h1>Gestión de Establecimientos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <button class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btn-toggle-filtros">
                    <i class="fas fa-filter me-1"></i>
                    <span class="texto-btn d-none d-md-inline">Mostrar filtros</span>
                </button>
                <button class="btn btn-success" id="btn-nuevo-establecimiento">
                    <i class="fas fa-plus"></i> Nuevo Establecimiento
                </button>
            </div>

            <div class="table-responsive" style="max-height: 600px;">
                <table class="table table-bordered table-striped nowrap" id="tabla-establecimientos" style="width:100%;">
                    <thead class="table-primary">
                        <tr>
                            <th>Empresa</th>
                            <th>Serie</th>
                            <th>Nombre comercial</th>
                            <th>Dirección</th>
                            <th>Logo</th>
                            <th>Factura</th>
                            <th>Nota_crédito</th>
                            <th>Nota_débito</th>
                            <th>Guía_remisión</th>
                            <th>Retención</th>
                            <th>Liquidación_compra</th>
                            <th>Proforma</th>
                            <th>Recibo</th>
                            <th>Ingreso</th>
                            <th>Egreso</th>
                            <th>Orden_Compra</th>
                            <th>Pedido</th>
                            <th>Consignación_Venta</th>
                            <th>Decimal_cantidad</th>
                            <th>decimal_precio</th>
                            <th>Estado</th>
                            <th>Editar</th>
                        </tr>
                        <tr id="fila-filtros" class="filters">
                            <th><input type="text" class="form-control form-control-sm" placeholder="Empresa"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Serie"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Nombre comercial"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Dirección"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Logo"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Factura"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Nota de crédito"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Nota de débito"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Guía de remisión"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Retención"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Liquidación de compras"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Proforma"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Recibo"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Ingreso"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Egreso"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Orden compra"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Pedido"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Consignación venta"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Decimal cantidad"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Decimal precio"></th>
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

    <div class="modal fade" id="modal-establecimiento" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <form id="form-establecimiento" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="establecimiento_id" id="establecimiento_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEstablecimientoLabel">Nuevo Establecimiento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="form-group col-md-12 mb-2">
                            <label for="empresa_id">Razón social</label>
                            <input type="hidden" name="ruc" id="ruc">
                            <select name="empresa_id" id="empresa_id" class="form-control-sm select2" style="width: 100%;" required></select>
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label>Serie</label>
                            <input type="text" name="serie" id="serie" class="form-control" required>
                        </div>
                        <div class="form-group col-md-9 mb-2">
                            <label>Nombre comercial</label>
                            <input type="text" name="nombre_comercial" id="nombre_comercial" class="form-control">
                        </div>
                        <div class="form-group col-md-12 mb-2">
                            <label>Dirección establecimiento</label>
                            <input type="text" name="direccion" id="direccion" class="form-control">
                        </div>
                        <div class="col-md-12 mb-2">
                            <h5 class="text-primary border-bottom pb-1">Secuenciales iniciales de documentos</h5>
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Factura</label>
                            <input type="number" name="factura" id="factura" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Nota de crédito</label>
                            <input type="number" name="nota_credito" id="nota_credito" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Nota de débito</label>
                            <input type="number" name="nota_debito" id="nota_debito" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Retención</label>
                            <input type="number" name="retencion" id="retencion" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Guía remisión</label>
                            <input type="number" name="guia_remision" id="guia_remision" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Liquidación c/s</label>
                            <input type="number" name="liquidacion_compra" id="liquidacion_compra" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Proforma</label>
                            <input type="number" name="proforma" id="proforma" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Recibo venta</label>
                            <input type="number" name="recibo" id="recibo" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Ingreso</label>
                            <input type="number" name="ingreso" id="ingreso" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Egreso</label>
                            <input type="number" name="egreso" id="egreso" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Orden compra</label>
                            <input type="number" name="orden_compra" id="orden_compra" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-2 mb-2">
                            <label>Pedido</label>
                            <input type="number" name="pedido" id="pedido" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label>Consignación venta</label>
                            <input type="number" name="consignacion_venta" id="consignacion_venta" class="form-control" value="1">
                        </div>

                        <div class="col-md-12 mb-2">
                            <h5 class="text-primary border-bottom pb-1">Decimales</h5>
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label>Cantidad</label>
                            <input type="number" name="decimal_cantidad" id="decimal_cantidad" class="form-control" value="2">
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label>Precio</label>
                            <input type="number" name="decimal_precio" id="decimal_precio" class="form-control" value="2">
                        </div>

                        <div class="col-md-12 mb-2">
                            <h5 class="text-primary border-bottom pb-1"></h5>
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            <label>Estado</label>
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="1" Selected>Activo</option>
                                <option value="0">Inactivo</option>
                              </select>
                        </div>
                        <div class="form-group col-md-8 mb-2">
                            <label>Logo</label>
                            <input type="file" name="logo" id="logo" class="form-control" accept=".jpg,.jpeg,image/jpeg">
                        </div>
                        <div class="form-group col-md-12 mb-2" id="preview-container" style="display:none;">
                            <label>Vista previa del logo</label><br>
                            <img id="preview-logo" src="" alt="Vista previa del logo" style="max-height: 120px;">
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

//para mostrar el logo cuando se lo carga
$('#logo').on('change', function (event) {
    const input = event.target;
    const file = input.files[0];

    if (file) {
        const validTypes = ['image/jpeg'];
        if (!validTypes.includes(file.type)) {
            Swal.fire('Archivo inválido', 'El logo debe ser un archivo JPG.', 'warning');
            $('#logo').val('');
            $('#preview-logo').attr('src', '');
            $('#preview-container').hide();
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            $('#preview-logo').attr('src', e.target.result);
            $('#preview-container').show();
        };
        reader.readAsDataURL(file);
    } else {
        $('#preview-logo').attr('src', '');
        $('#preview-container').hide();
    }
});


//para esconder el logo
$('#modal-establecimiento').on('hidden.bs.modal', function () {
    $('#preview-logo').attr('src', '');
    $('#preview-container').hide();
    $('#form-establecimiento')[0].reset();
    $('#empresa_id').val(null).trigger('change');
});


$('#empresa_id').on('select2:select', function (e) {
    const idEmpresa = e.params.data.id;

    $.ajax({
        url: `/empresas/${idEmpresa}`,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            $('#ruc').val(data.ruc ?? '');

            // Dar foco al input serie
            setTimeout(() => {
                $('#serie').focus();
            }, 100); // pequeña pausa para asegurar que el DOM esté listo
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            Swal.fire('Error', 'No se pudo obtener el RUC de la empresa', 'error');
        }
    });
});


$(document).ready(function () {
    $('#serie').inputmask('999-999');

    //para poner el ruc de la empresa seleccionada y luego buscar informacion de ese ruc
    $('#empresa_id').select2({
        dropdownParent: $('#modal-establecimiento'), // para funcionar bien dentro del modal
        placeholder: 'Seleccione una empresa',
        ajax: {
            url: '{{ route("empresas.buscar") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // lo que escribe el usuario
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return { id: item.id, text: item.razon_social };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });
});

    $(function () {
        let tabla = $('#tabla-establecimientos').DataTable({
        dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
        processing: false,
        serverSide: true,
        fixedHeader: true,
        autoWidth: true,
        ajax: {
            url: '{{ route("establecimientos.data") }}',
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
                { data: 'empresa.razon_social', name: 'empresa.razon_social' },
                { data: 'serie'},
                { data: 'nombre_comercial'},
                { data: 'direccion'},
                { data: 'logo_img', name: 'logo', orderable: false, searchable: false },
                { data: 'factura' },
                { data: 'nota_credito'},
                { data: 'nota_debito'},
                { data: 'guia_remision'},
                { data: 'retencion'},
                { data: 'liquidacion_compra'},
                { data: 'proforma'},
                { data: 'recibo'},
                { data: 'ingreso'},
                { data: 'egreso'},
                { data: 'orden_compra'},
                { data: 'pedido'},
                { data: 'consignacion_venta'},
                { data: 'decimal_cantidad'},
                { data: 'decimal_precio'},
                { data: 'estado'},
                { data: 'acciones', orderable: false, searchable: false },
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

    $('#btn-nuevo-establecimiento').on('click', function () {
        $('#form-establecimiento')[0].reset();
        $('#establecimiento_id').val('');
        $('#empresa_id').val(null).trigger('change'); // limpia select2
        $('#modalEstablecimientoLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo establecimiento');
        $('#modal-establecimiento').modal('show');
    });


         // Guardar o editar
         $('#form-establecimiento').on('submit', function (e) {
            e.preventDefault();

            const id = $('#establecimiento_id').val();
            const url = id ? `/establecimientos/update/${id}` : '{{ route("establecimientos.store") }}';
            const method = id ? 'POST' : 'POST';

            const formData = new FormData();

            // Campo oculto _method solo si es actualización
            if (id) formData.append('_method', 'PUT');

            // Token CSRF
            formData.append('_token', $('input[name="_token"]').val());

            // Archivo logo
            const archivoLogo = $('#logo')[0].files[0];
            if (archivoLogo) {
                formData.append('logo', archivoLogo);
            }

            // Campos del formulario
            formData.append('empresa_id', $('#empresa_id').val());
            formData.append('serie', $('#serie').val());
            formData.append('nombre_comercial', $('#nombre_comercial').val());
            formData.append('direccion', $('#direccion').val());
            formData.append('estado', $('#estado').val());

            const camposNumericos = [
                'factura', 'nota_credito', 'nota_debito', 'guia_remision', 'retencion',
                'liquidacion_compra', 'proforma', 'recibo', 'ingreso', 'egreso',
                'orden_compra', 'pedido', 'consignacion_venta', 'decimal_cantidad', 'decimal_precio'
            ];

            camposNumericos.forEach(campo => {
                formData.append(campo, $('#' + campo).val());
            });

            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    $('#modal-establecimiento').modal('hide');
                    $('#tabla-establecimientos').DataTable().ajax.reload();

                    Swal.fire({
                        icon: 'success',
                        title: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errores = xhr.responseJSON.errors;
                        let mensaje = '';
                        for (let campo in errores) {
                            mensaje += errores[campo][0] + '<br>';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Errores de validación',
                            html: mensaje
                        });
                    } else {
                        Swal.fire('Error', 'Hubo un problema al guardar el establecimiento.', 'error');
                    }
                }
            });
        });

        // Editar
        $(document).on('click', '.editar-establecimiento', function () {
            const id = $(this).data('id');

            $.ajax({
                url: `/establecimientos/edit/${id}`,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    // ID oculto
                    $('#establecimiento_id').val(data.id);

                    // Preseleccionar empresa en Select2
                    const empresaOption = new Option(data.empresa.razon_social, data.empresa_id, true, true);
                    $('#empresa_id').append(empresaOption).trigger('change');

                    // Campos principales
                    //$('#ruc').val(data.ruc);
                    $('#ruc').val(data.empresa.ruc ?? '');
                    $('#serie').val(data.serie);
                    $('#nombre_comercial').val(data.nombre_comercial);
                    $('#direccion').val(data.direccion);
                    $('#estado').val(data.estado ? '1' : '0').trigger('change');

                    // Secuenciales
                    $('#factura').val(data.factura);
                    $('#nota_credito').val(data.nota_credito);
                    $('#nota_debito').val(data.nota_debito);
                    $('#retencion').val(data.retencion);
                    $('#guia_remision').val(data.guia_remision);
                    $('#liquidacion_compra').val(data.liquidacion_compra);
                    $('#proforma').val(data.proforma);
                    $('#recibo').val(data.recibo);
                    $('#ingreso').val(data.ingreso);
                    $('#egreso').val(data.egreso);
                    $('#orden_compra').val(data.orden_compra);
                    $('#pedido').val(data.pedido);
                    $('#consignacion_venta').val(data.consignacion_venta);
                    $('#decimal_cantidad').val(data.decimal_cantidad);
                    $('#decimal_precio').val(data.decimal_precio);

                    // Logo preview
                    if (data.logo) {
                        const logoUrl = `/storage/logos_establecimientos/${data.logo}`;
                        $('#preview-logo').attr('src', logoUrl);
                        $('#preview-container').show();
                    } else {
                        $('#preview-logo').attr('src', '');
                        $('#preview-container').hide();
                    }

                    // Cambiar título del modal
                    $('#modalEstablecimientoLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar Establecimiento');

                    // Mostrar modal
                    $('#modal-establecimiento').modal('show');
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    Swal.fire('Error', 'No se pudo cargar la información del establecimiento.', 'error');
                }
            });
        });




//para buscar los datos del ruc con la api
$('#serie').on('change', function () {
    const numeroEstablecimiento = $(this).val().trim().substring(0, 3);
    const ruc = $('#ruc').val()?.trim();

    if (!ruc || ruc.length !== 13) {
        Swal.fire('Atención', 'Seleccione una empresa para buscar la información del establecimiento.', 'warning');
        $('#nombre_comercial').val('');
        $('#direccion').val('');
        $('#estado').val('');
        $('#serie').val('');
        return;
    }

    if (numeroEstablecimiento.length === 3 && ruc.length === 13) {
        Swal.fire({
            title: 'Consultando establecimiento...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: 'http://137.184.159.242:4000/api/sri-identification',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({ identification: ruc }),
            success: function (response) {
                Swal.close();

                const establecimientos = response.data?.establecimientos ?? [];
                const contribuyente = response.data?.datosContribuyente?.[0];
                const razonSocial = contribuyente?.razonSocial ?? '';

                const establecimiento = establecimientos.find(est => est.numeroEstablecimiento === numeroEstablecimiento);

                if (establecimiento) {
                    const nombreComercial = establecimiento.nombreFantasiaComercial?.trim();
                    $('#nombre_comercial').val(nombreComercial !== '' ? nombreComercial : razonSocial);
                    $('#direccion').val(establecimiento.direccionCompleta ?? '');
                    $('#estado').val(establecimiento.estado === 'ABIERTO' ? '1' : '0');
                } else {
                    Swal.fire('Advertencia', `No se encontró el establecimiento ${numeroEstablecimiento} para el RUC ${ruc}.`, 'warning');
                    $('#nombre_comercial').val('');
                    $('#direccion').val('');
                    $('#estado').val('0');
                }
            },
            error: function (xhr) {
                Swal.close();
                Swal.fire('Error', 'No se pudo obtener información del RUC', 'error');
                console.log(xhr.responseText);
            }
        });
    }
});

});

</script>
@stop
