<!-- resources/views/partials/modal_cliente.blade.php -->
<!-- Modal -->

<div class="modal fade" id="modal-cliente" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-cliente">
            @csrf
            <input type="hidden" name="cliente_id" id="cliente_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClienteLabel">Gestión de Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Pestañas con íconos -->
                    <ul class="nav nav-tabs mb-3" id="clienteTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-general">
                                <i class="fas fa-user me-1"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-financieros">
                                <i class="fas fa-dollar-sign me-1"></i> Financiero
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-tributarios">
                                <i class="fas fa-file-invoice me-1"></i> Tributario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-comercial">
                                <i class="fas fa-briefcase me-1"></i> Comercial
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-documentos">
                                <i class="fas fa-folder-open me-1"></i> Documentos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-kpi">
                                <i class="fas fa-chart-line me-1"></i> KPIs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-configuracion">
                                <i class="fas fa-cogs me-1"></i> Configuración
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        @include('empresa.clientes.tabs.general')
                        @include('empresa.clientes.tabs.financieros')
                        @include('empresa.clientes.tabs.tributarios')
                        @include('empresa.clientes.tabs.comercial')
                        @include('empresa.clientes.tabs.documentos')
                        @include('empresa.clientes.tabs.kpi')
                        @include('empresa.clientes.tabs.configuracion')
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


@push('js')
<script>
    $(document).ready(function () {
    $('#telefono').inputmask('0999999999');

    // Al cerrar el modal, limpiar todos los campos del formulario
    $('#modal-cliente').on('hidden.bs.modal', function () {
        const form = $(this).find('form')[0];
        form.reset();
        $(form).find('input[type=hidden]').not('[name="_token"]').val('');
        $(form).find('input[type=checkbox]').prop('checked', false);
        $(form).find('select').val('').trigger('change');
    });

    // Al mostrar el modal, activar la pestaña General
    $('#modal-cliente').on('show.bs.modal', function () {
        $(this).find('.nav-tabs a[href="#tab-general"]').tab('show');
    });


    // Abrir modal nuevo cliente
    $('#btn-nuevo-cliente').on('click', function () {
        const form = $('#form-cliente')[0];
        form.reset();

        $('#form-cliente').find('input[type=hidden]').not('[name="_token"]').val('');
        $('#form-cliente').find('input[type=checkbox]').prop('checked', false);
        $('#form-cliente').find('select.select2').val('').trigger('change');
        $('#cliente_id').val('');

        // --- LIMPIEZA COMPLETA DEL TAB DOCUMENTOS ---
        // Limpiar input file
        $('#documentos-container input[type="file"]').val('');

        // Limpiar tipos generados
        $('#documentos-container #tipos-container').html('');

        // Remover documentos previamente cargados
        $('#documentos-container .documento-item').remove();

        // Limpiar documentos-guardados por si acaso
        $('#documentos-guardados').html('');

        $('#modalClienteLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo Cliente');
        $('#modal-cliente').modal('show');
    });



    // Autocompletado por identificación
    $('#numero_identificacion').on('change', function () {
        const numero_identificacion = $(this).val();
        const tipo_identificacion = $('#tipo_identificacion').val();
        $('#codigo_interno').val(numero_identificacion);

        const esCedulaValida = tipo_identificacion === '05' && numero_identificacion.length === 10;
        const esRucValido = tipo_identificacion === '04' && numero_identificacion.length === 13;

        if (esCedulaValida || esRucValido) {
            $.get('{{ route("clientes.buscarPorIdentificacion") }}', { numero_identificacion }, function (data) {
                if (data.encontrado) {
                    const p = data.persona;
                    $('#nombre').val(p.nombre ?? '');
                    $('#estado').val(p.datos_cliente?.estado ?? 'activo');
                    $('#pais').val(p.pais ?? '');
                    $('#provincia').val(p.provincia ?? '');
                    $('#ciudad').val(p.ciudad ?? '');
                    $('#zona').val(p.zona ?? '');
                    $('#direccion').val(p.direccion ?? '');
                    $('#email').val(p.email ?? '');
                } else {
                    Swal.fire({
                        title: 'Consultando SRI...',
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
                                const c = response.data?.datosContribuyente?.[0];
                                const establecimientos = response.data?.establecimientos ?? [];
                                if (c) {
                                    $('#nombre').val(c.razonSocial ?? '');
                                    $('#estado').val(c.estadoContribuyenteRuc === 'ACTIVO' ? 'activo' : 'inactivo');
                                    $('#agente_retencion').prop('checked', (c.agenteRetencion || '').toUpperCase() === 'SI');
                                    $('#contribuyente_especial').prop('checked', (c.contribuyenteEspecial || '').toUpperCase() === 'SI');
                                    $('#obligado_contabilidad').prop('checked', (c.obligadoLlevarContabilidad || '').toUpperCase() === 'SI');

                                    const regimenMap = {
                                        'GENERAL': '1',
                                        'RIMPE EMPRENDEDOR': '2',
                                        'RIMPE NEGOCIO POPULAR': '3'
                                    };

                                    $('#regimen_tributario').val(regimenMap[(c.regimen || '').toUpperCase()] || '');
                                }

                                const matriz = establecimientos.find(est => est.matriz === 'SI');
                                if (matriz?.direccionCompleta) {
                                    const partes = matriz.direccionCompleta.split(' / ');
                                    $('#provincia').val(partes[0] ?? '');
                                    $('#ciudad').val(partes[1] ?? '');
                                    $('#zona').val(partes[2] ?? '');
                                    $('#direccion').val(partes[3] ?? '');
                                }
                            }

                            if (esCedulaValida) {
                                const c = response.data;
                                $('#nombre').val(c.nombreCompleto ?? '');
                            }
                        },
                        error: function () {
                            Swal.close();
                            Swal.fire('Error', 'No se pudo obtener información del SRI', 'error');
                        }
                    });
                }
            });
        }
    });

    // Guardar o editar cliente
$('#form-cliente').on('submit', function (e) {
    e.preventDefault();
    const id = $('#cliente_id').val();
    const url = id ? `/empresa/clientes/${id}` : '{{ route("clientes.store") }}';
    const method = id ? 'POST' : 'POST'; // Laravel no acepta PUT con FormData directamente

    const formData = new FormData(this);
    if (id) {
        formData.append('_method', 'PUT'); // Spoofing method for Laravel
    }

    $.ajax({
        url: url,
        method: method,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            $('#modal-cliente').modal('hide');
            Swal.fire({
                icon: 'success',
                title: response.message,
                toast: true,
                timer: 1500,
                position: 'top-end',
                showConfirmButton: false
            });

            $('#tabla-clientes').DataTable().ajax.reload(null, false);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let messages = '';
                Object.keys(errors).forEach(key => {
                    messages += `<li>${errors[key][0]}</li>`;
                });

                Swal.fire({
                    icon: 'error',
                    html: `<ul class="text-left">${messages}</ul>`,
                    title: 'Errores de validación',
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Ocurrió un error inesperado'
                });
            }
        }
    });
});


    // Cargar cliente en edición
$(document).on('click', '.editar-cliente', function () {
    const id = $(this).data('id');
    const url = `/empresa/clientes/${id}/edit`;

    $.get(url, function (res) {
        const datos = res.datos_cliente ?? {};

        $('#cliente_id').val(res.id);
        $('#tipo_identificacion').val(res.tipo_identificacion).trigger('change');
        $('#numero_identificacion').val(res.numero_identificacion);
        $('#nombre').val(res.nombre);
        $('#telefono').val(res.telefono);
        $('#email').val(res.email);
        $('#direccion').val(res.direccion);
        $('#provincia').val(res.provincia);
        $('#ciudad').val(res.ciudad);
        $('#pais').val(res.pais);
        $('#estado').val(datos.estado ?? 'activo');

        // Datos cliente
        $('#codigo_interno').val(datos.codigo_interno ?? '');
        $('#categoria_cliente').val(datos.categoria_cliente ?? '');
        $('#segmento').val(datos.segmento ?? '');
        $('#fecha_registro').val(datos.fecha_registro ?? '');
        $('#vendedor_asignado').val(datos.vendedor_asignado ?? '').trigger('change');
        $('#lista_precios').val(datos.lista_precios ?? '').trigger('change');
        $('#canal_venta').val(datos.canal_venta ?? '');
        $('#zona').val(datos.zona ?? '');
        $('#clasificacion').val(datos.clasificacion ?? '');
        $('#inicio_relacion').val(datos.inicio_relacion ?? '');

        // Configuración
        $('#notas').val(datos.configuracion?.notas ?? '');
        $('#permitir_venta_con_deuda').prop('checked', datos.configuracion?.permitir_venta_con_deuda ?? false);
        $('#aplica_descuento').prop('checked', datos.configuracion?.aplica_descuento ?? false);

        // Financieros
        $('#cupo_credito').val(datos.financieros?.cupo_credito ?? '');
        $('#dias_credito').val(datos.financieros?.dias_credito ?? '');
        $('#forma_pago').val(datos.financieros?.forma_pago ?? '').trigger('change');
        $('#observaciones_crediticias').val(datos.financieros?.observaciones_crediticias ?? '');
        $('#nivel_riesgo').val(datos.financieros?.nivel_riesgo ?? '');

        // Tributarios
        $('#agente_retencion').prop('checked', datos.tributarios?.agente_retencion ?? false);
        $('#contribuyente_especial').prop('checked', datos.tributarios?.contribuyente_especial ?? false);
        $('#obligado_contabilidad').prop('checked', datos.tributarios?.obligado_contabilidad ?? false);
        $('#regimen_tributario').val(datos.tributarios?.regimen_tributario ?? '');
        $('#retencion_fuente').val(datos.tributarios?.retencion_fuente ?? '');
        $('#retencion_iva').val(datos.tributarios?.retencion_iva ?? '');

        // KPI
        $('#total_ventas').val(datos.kpi?.total_ventas ?? '');
        $('#ultima_compra_fecha').val(datos.kpi?.ultima_compra_fecha ?? '');
        $('#ultima_compra_monto').val(datos.kpi?.ultima_compra_monto ?? '');
        $('#dias_promedio_pago').val(datos.kpi?.dias_promedio_pago ?? '');
        $('#saldo_por_cobrar').val(datos.kpi?.saldo_por_cobrar ?? '');
        $('#promedio_mensual').val(datos.kpi?.promedio_mensual ?? '');

        // Documentos
        const documentos = datos.documentos ?? [];
        const contenedor = $('#documentos-container');
        contenedor.find('.documento-item').remove();
        documentos.forEach(doc => {
            contenedor.append(`
                <div class="col-md-6 documento-item mb-3">
                    <div class="card border shadow-sm p-2">
                        <p class="mb-1"><strong>Tipo:</strong> ${doc.tipo ?? 'N/A'}</p>
                        <p class="mb-2"><strong>Archivo:</strong>
                            <a href="/storage/${doc.archivo}" target="_blank">Ver Documento</a>
                        </p>
                        <button type="button" class="btn btn-sm btn-danger eliminar-documento" data-id="${doc.id}">
                            Eliminar
                        </button>
                    </div>
                </div>
            `);
        });

        $('#modalClienteLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar Cliente');
        $('#modal-cliente').modal('show');
    }).fail(function () {
        Swal.fire('Error', 'No se pudo cargar el cliente', 'error');
    });
});



    // Eliminar cliente
    $(document).on('click', '.eliminar-cliente', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el cliente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/empresa/clientes/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            toast: true,
                            timer: 1500,
                            position: 'top-end',
                            showConfirmButton: false
                        });
                        $('#tabla-clientes').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'No se pudo eliminar el cliente'
                        });
                    }
                });
            }
        });
    });
});

//para eliminar los documentos del clientes
$(document).on('click', '.eliminar-documento', function () {
    const id = $(this).data('id');
    Swal.fire({
        title: '¿Eliminar documento?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/empresa/clientes/documentos/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    Swal.fire('Eliminado', response.message, 'success');
                    $(`.eliminar-documento[data-id="${id}"]`).closest('.documento-item').remove();
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el documento', 'error');
                }
            });
        }
    });
});


</script>
@endpush
