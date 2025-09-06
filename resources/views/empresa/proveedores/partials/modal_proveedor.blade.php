<style>
    #proveedorTabs {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: hidden;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    #proveedorTabs:hover {
        overflow-x: auto;
    }

    #proveedorTabs .nav-link {
        white-space: nowrap;
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }

    /* Opcional: ocultar feo scrollbar en algunos navegadores */
    #proveedorTabs::-webkit-scrollbar {
        height: 5px;
    }

    #proveedorTabs::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.3);
        border-radius: 3px;
    }

    #proveedorTabs::-webkit-scrollbar-track {
        background-color: transparent;
    }
</style>


<div class="modal fade" id="modal-proveedor" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modalProveedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-proveedor">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" class="exclude-reset">
            <input type="hidden" name="proveedor_id" id="proveedor_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProveedorLabel">Gestión de Proveedor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Pestañas horizontales -->
                    <ul class="nav nav-tabs mb-3" id="proveedorTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-general-tab" data-toggle="tab" href="#tab-general" role="tab">
                                <i class="fas fa-user me-1"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-financieros-tab" data-toggle="tab" href="#tab-financieros" role="tab">
                                <i class="fas fa-dollar-sign me-1"></i> Financiero
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-contable-tab" data-toggle="tab" href="#tab-contable" role="tab">
                                <i class="fas fa-calculator me-1"></i> Contable
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-tributarios-tab" data-toggle="tab" href="#tab-tributarios" role="tab">
                                <i class="fas fa-file-invoice me-1"></i> Tributario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-comercial-tab" data-toggle="tab" href="#tab-comercial" role="tab">
                                <i class="fas fa-briefcase me-1"></i> Comercial
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-documentos-tab" data-toggle="tab" href="#tab-documentos" role="tab">
                                <i class="fas fa-folder-open me-1"></i> Documentos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-kpi-tab" data-toggle="tab" href="#tab-kpi" role="tab">
                                <i class="fas fa-chart-line me-1"></i> KPIs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-configuracion-tab" data-toggle="tab" href="#tab-configuracion" role="tab">
                                <i class="fas fa-cogs me-1"></i> Configuración
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        @include('empresa.proveedores.tabs.general')
                        @include('empresa.proveedores.tabs.financieros')
                        @include('empresa.proveedores.tabs.contables')
                        @include('empresa.proveedores.tabs.tributarios')
                        @include('empresa.proveedores.tabs.comercial')
                        @include('empresa.proveedores.tabs.documentos')
                        @include('empresa.proveedores.tabs.kpi')
                        @include('empresa.proveedores.tabs.configuracion')
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
    // Limpiar formulario al cambiar tipo de identificación (excepto el campo mismo)
        $('#tipo_identificacion').on('change', function () {
        const form = $('#form-proveedor');
        form.find('input:not([name="_token"], [name="tipo_identificacion"], [name="proveedor_id"])').val('');
        form.find('input[type=checkbox]').prop('checked', false);
        form.find('select.select2').not('#tipo_identificacion').val('').trigger('change');
        //$('#proveedor_id').val('');
        $('#documentos-container input[type="file"]').val('');
        $('#documentos-container #tipos-container').html('');
        $('#documentos-container .documento-item').remove();
        $('#documentos-guardados').html('');
    });

            //para que los selects me permitan buscar
        $.fn.modal.Constructor.prototype._enforceFocus = function() {};
        $('#modal-proveedor').on('shown.bs.modal', function () {
            $(this).find('.select2bs4').select2({
                theme: 'bootstrap4',
                placeholder: 'Seleccione una opción',
                allowClear: true,
                minimumResultsForSearch: 5 // 🔥 Mostrar búsqueda solo si hay más de 5 opciones
            });
        });

    $('#telefono').inputmask('0999999999');

    // Al cerrar el modal, limpiar todos los campos del formulario
    $('#modal-proveedor').on('hidden.bs.modal', function () {
        const form = $(this).find('form')[0];
        form.reset();
        $(form).find('input[type=hidden]').not('[name="_token"]').val('');
        $(form).find('input[type=checkbox]').prop('checked', false);
        $(form).find('select').val('').trigger('change');
    });

    // Al mostrar el modal, activar la pestaña General
    $('#modal-proveedor').on('show.bs.modal', function () {
        $(this).find('.nav-tabs a[href="#tab-general"]').tab('show');
    });


    // Abrir modal nuevo proveedor
    $('#btn-nuevo-proveedor').on('click', function () {
        const form = $('#form-proveedor')[0];
        form.reset();

        $('#form-proveedor').find('input[type=hidden]').not('[name="_token"]').val('');
        $('#form-proveedor').find('input[type=checkbox]').prop('checked', false);
        $('#form-proveedor').find('select.select2').val('').trigger('change');
        $('#proveedor_id').val('');

        // --- LIMPIEZA COMPLETA DEL TAB DOCUMENTOS ---
        // Limpiar input file
        $('#documentos-container input[type="file"]').val('');

        // Limpiar tipos generados
        $('#documentos-container #tipos-container').html('');

        // Remover documentos previamente cargados
        $('#documentos-container .documento-item').remove();

        // Limpiar documentos-guardados por si acaso
        $('#documentos-guardados').html('');

        $('#modalProveedorLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo Proveedor');
        $('#modal-proveedor').modal('show');
    });



    // Autocompletado por identificación
    $('#numero_identificacion').on('change', function () {
        const numero_identificacion = $(this).val();
        const tipo_identificacion = $('#tipo_identificacion').val();
        $('#codigo_interno').val(numero_identificacion);

        const esCedulaValida = tipo_identificacion === '05' && numero_identificacion.length === 10;
        const esRucValido = tipo_identificacion === '04' && numero_identificacion.length === 13;

        if (esCedulaValida || esRucValido) {
            $.get('{{ route("personas.buscarPorIdentificacion") }}', { numero_identificacion }, function (data) {
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

    // Guardar o editar proveedor
$('#form-proveedor').on('submit', function (e) {
    e.preventDefault();

    const btn = $(this).find('button[type="submit"]');
    btn.prop('disabled', true);

    const id = $('#proveedor_id').val();
    const url = id
        ? `/empresa/proveedores/${id}`
        : '{{ route("proveedores.store") }}';
    const method = id ? 'POST' : 'POST';

    const formData = new FormData(this);
    if (id) {
        formData.append('_method', 'PUT'); // Spoofing PUT
    }

    $.ajax({
        url: url,
        method: method,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            $('#modal-proveedor').modal('hide');

            Swal.fire({
                icon: 'success',
                title: response.message || 'Proveedor registrado correctamente.',
                toast: true,
                timer: 1500,
                position: 'top-end',
                showConfirmButton: false
            });

            // ✅ Reset formulario
            $('#form-proveedor')[0].reset();
            $('#form-proveedor').find('input:hidden').not('[name="_token"]').val('');
            $('#form-proveedor').find('select').val(null).trigger('change');
            $('#form-proveedor').find('input:checkbox').prop('checked', false);
            $('#form-proveedor').find('.is-invalid').removeClass('is-invalid');
            $('#form-proveedor').find('.error-message').remove();

            $('#tabla-proveedores').DataTable().ajax.reload(null, false);
        },
        error: function (xhr) {
            btn.prop('disabled', false);

            if (xhr.status === 422) {
                if (xhr.responseJSON?.errors) {
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
                } else if (xhr.responseJSON?.message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON.message,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Ocurrió un error inesperado'
                });
            }
        },
        complete: function () {
            btn.prop('disabled', false);
        }
    });
});



    // Cargar proveedor en edición
$(document).on('click', '.editar-proveedor', function () {
    const id = $(this).data('id');
    const url = `/empresa/proveedores/${id}/edit`;

    $.get(url, function (res) {
        const datos = res.datos_proveedor ?? {};
        $('#proveedor_id').val(res.id);
        $('#tipo_identificacion').val(res.tipo_identificacion).trigger('change');
        $('#numero_identificacion').val(res.numero_identificacion);
        $('#nombre').val(res.nombre);
        $('#nombre_comercial').val(res.nombre_comercial);
        $('#telefono').val(res.telefono);
        $('#email').val(res.email);
        $('#direccion').val(res.direccion);
        $('#provincia').val(res.provincia);
        $('#ciudad').val(res.ciudad);
        $('#pais').val(res.pais);
        $('#estado').val(datos.estado ?? 'activo');
        $('#id_banco').val(res.id_banco ?? '');
        $('#tipo_cuenta').val(res.tipo_cuenta ?? '');
        $('#numero_cuenta').val(res.numero_cuenta ?? '');

        // Datos proveedor
        $('#codigo_interno').val(datos.codigo_interno ?? '');
        $('#categoria_proveedor').val(datos.categoria_proveedor ?? '');
        $('#segmento').val(datos.segmento ?? '');
        $('#comprador_asignado').val(datos.comprador_asignado ?? '').trigger('change');
        $('#zona').val(datos.zona ?? '');
        $('#clasificacion').val(datos.clasificacion ?? '');
        $('#inicio_relacion').val(datos.inicio_relacion_formatted ?? '');

        // Configuración
        $('#notas').val(datos.configuracion?.notas ?? '');

        // Financieros
        $('#limite_credito').val(datos.financieros?.limite_credito ?? '');
        $('#dias_credito').val(datos.financieros?.dias_credito ?? '');
        $('#forma_pago').val(datos.financieros?.forma_pago ?? '').trigger('change');
        $('#observaciones_crediticias').val(datos.financieros?.observaciones_crediticias ?? '');
        $('#historial_pagos').val(datos.financieros?.historial_pagos ?? '');
        $('#nivel_riesgo').val(datos.financieros?.nivel_riesgo ?? '');

        // Tributarios
        $('#agente_retencion').prop('checked', datos.tributarios?.agente_retencion ?? false);
        $('#contribuyente_especial').prop('checked', datos.tributarios?.contribuyente_especial ?? false);
        $('#obligado_contabilidad').prop('checked', datos.tributarios?.obligado_contabilidad ?? false);
        $('#parte_relacionada').prop('checked', datos.tributarios?.parte_relacionada ?? false);
        $('#regimen_tributario').val(datos.tributarios?.regimen_tributario ?? '');
        $('#codigo_tipo_proveedor_sri').val(datos.tributarios?.codigo_tipo_proveedor_sri ?? '');
        $('#retencion_fuente').val(datos.tributarios?.retencion_fuente ?? '');
        $('#retencion_iva').val(datos.tributarios?.retencion_iva ?? '');

        // KPI
        $('#total_compras_anual').val(datos.kpi?.total_compras_anual ?? '');
        $('#cantidad_facturas').val(datos.kpi?.cantidad_facturas ?? '');
        $('#monto_promedio_compra').val(datos.kpi?.monto_promedio_compra ?? '');
        $('#ultima_compra_fecha').val(datos.kpi?.ultima_compra_fecha ?? '');
        $('#ultima_compra_monto').val(datos.kpi?.ultima_compra_monto ?? '');
        $('#dias_promedio_pago').val(datos.kpi?.dias_promedio_pago ?? '');
        $('#porcentaje_entregas_a_tiempo').val(datos.kpi?.porcentaje_entregas_a_tiempo ?? '');
        $('#porcentaje_entregas_fuera_plazo').val(datos.kpi?.porcentaje_entregas_fuera_plazo ?? '');
        $('#porcentaje_devoluciones').val(datos.kpi?.porcentaje_devoluciones ?? '');
        $('#porcentaje_reclamos').val(datos.kpi?.porcentaje_reclamos ?? '');
        $('#cantidad_incidentes').val(datos.kpi?.cantidad_incidentes ?? '');
        $('#saldo_por_pagar').val(datos.kpi?.saldo_por_pagar ?? '');
        $('#promedio_mensual').val(datos.kpi?.promedio_mensual ?? '');
        $('#productos_frecuentes').val(datos.kpi?.productos_frecuentes ?? '');

        // Contables
        $('#cuenta_por_pagar').val(datos.contables?.cuenta_por_pagar ?? '');
        $('#cuenta_gasto_predeterminada').val(datos.contables?.cuenta_gasto_predeterminada ?? '');
        $('#cuenta_inventario_predeterminada').val(datos.contables?.cuenta_inventario_predeterminada ?? '');
        $('#cuenta_anticipo').val(datos.contables?.cuenta_anticipo ?? '');
        $('#centro_costo').val(datos.contables?.centro_costo ?? '');
        $('#proyecto').val(datos.contables?.proyecto ?? '');
        $('#cuenta_anticipo').val(datos.contables?.cuenta_anticipo ?? '');

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

        $('#modalProveedorLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar Proveedor');
        $('#modal-proveedor').modal('show');
    }).fail(function () {
        Swal.fire('Error', 'No se pudo cargar el proveedor', 'error');
    });
});

    // Eliminar proveedor
    $(document).on('click', '.eliminar-proveedor', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el proveedor.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/empresa/proveedores/${id}`,
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
                        $('#tabla-proveedores').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'No se pudo eliminar el proveedor'
                        });
                    }
                });
            }
        });
    });
});

//para eliminar los documentos del proveedores
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
                url: `/empresa/proveedores/documentos/${id}`,
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
