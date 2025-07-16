<!-- resources/views/partials/modal_cliente.blade.php -->
<!-- Modal -->

<div class="modal fade" id="modal-vendedor" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-vendedor">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" class="exclude-reset">
            <input type="hidden" name="vendedor_id" id="vendedor_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVendedorLabel">Gestión de Vendedor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Pestañas con íconos -->
                    <ul class="nav nav-tabs mb-3" id="vendedorTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-general">
                                <i class="fas fa-user me-1"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-comercial">
                                <i class="fas fa-dollar-sign me-1"></i> Comercial
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-financieros">
                                <i class="fas fa-dollar-sign me-1"></i> Financiero
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        @include('empresa.vendedores.tabs.general')
                        @include('empresa.vendedores.tabs.comercial')
                        @include('empresa.vendedores.tabs.financieros')
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
    $('#modal-vendedor').on('hidden.bs.modal', function () {
        const form = $(this).find('form')[0];
        form.reset();
        $(form).find('input[type=hidden]').not('[name="_token"]').val('');
        $(form).find('input[type=checkbox]').prop('checked', false);
        $(form).find('select').val('').trigger('change');
    });

    // Al mostrar el modal, activar la pestaña General
    $('#modal-vendedor').on('show.bs.modal', function () {
        $(this).find('.nav-tabs a[href="#tab-general"]').tab('show');
    });


    // Abrir modal nuevo vendedor
    $('#btn-nuevo-vendedor').on('click', function () {
        const form = $('#form-vendedor')[0];
        form.reset();

        $('#form-vendedor').find('input[type=hidden]').not('[name="_token"]').val('');
        $('#form-vendedor').find('input[type=checkbox]').prop('checked', false);
        $('#form-vendedor').find('select.select2').val('').trigger('change');
        $('#vendedor_id').val('');
        $('#modalVendedorLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo Vendedor');
        $('#modal-vendedor').modal('show');
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
                    $('#estado').val(p.datos_vendedor?.estado ?? 'activo');
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

    // Guardar o editar vendedor
$('#form-vendedor').on('submit', function (e) {
    e.preventDefault();

    const id = $('#vendedor_id').val();
    const url = id
        ? `/empresa/vendedores/${id}`
        : '{{ route("vendedores.store") }}';

    const method = 'POST'; // Laravel no acepta PUT con FormData directamente

    const formData = new FormData(this);
    if (id) {
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url: url,
        method: method,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            $('#modal-vendedor').modal('hide');

            Swal.fire({
                icon: 'success',
                title: response.message || 'Vendedor guardado correctamente.',
                toast: true,
                timer: 1500,
                position: 'top-end',
                showConfirmButton: false
            });

            // ✅ Limpiar el formulario
            const form = $('#form-vendedor')[0];
            form.reset();
            $('#form-vendedor').find('input[type=hidden]').not('[name="_token"]').val('');
            $('#form-vendedor').find('input:checkbox').prop('checked', false);
            $('#form-vendedor').find('select').val('').trigger('change');
            $('#form-vendedor').find('.is-invalid').removeClass('is-invalid');
            $('#form-vendedor').find('.error-message').remove();

            $('#tabla-vendedores').DataTable().ajax.reload(null, false);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                // Errores de validación
                if (xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    let messages = '';
                    Object.keys(errors).forEach(key => {
                        messages += `<li>${errors[key][0]}</li>`;
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Errores de validación',
                        html: `<ul class="text-left">${messages}</ul>`,
                    });
                } else if (xhr.responseJSON?.message) {
                    // Error general enviado como message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON.message,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado.',
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Ocurrió un error inesperado.',
                });
            }
        }
    });
});



    // Cargar vendedor en edición
$(document).on('click', '.editar-vendedor', function () {
    const id = $(this).data('id');
    const url = `/empresa/vendedores/${id}/edit`;

    $.get(url, function (res) {
        const datos = res.datos_vendedor ?? {};

        $('#vendedor_id').val(res.id);
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

        // Datos vendedor
        $('#codigo_interno').val(datos.codigo_interno ?? '');
        $('#perfil').val(datos.perfil ?? '');
        $('#fecha_registro').val(datos.fecha_registro ?? '');
        $('#zona').val(datos.zona ?? '');
        $('#inicio_relacion').val(datos.inicio_relacion ?? '');
        $('#informacion_adicional').val(datos.informacion_adicional ?? '');
        $('#monto_ventas_asignado').val(datos.monto_ventas_asignado ?? '');

        $('#modalVendedorLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar Vendedor');
        $('#modal-vendedor').modal('show');
    }).fail(function () {
        Swal.fire('Error', 'No se pudo cargar el vendedor', 'error');
    });
});



    // Eliminar vendedor
    $(document).on('click', '.eliminar-vendedor', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el vendedor.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/empresa/vendedores/${id}`,
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
                        $('#tabla-vendedores').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'No se pudo eliminar el vendedor'
                        });
                    }
                });
            }
        });
    });
});

</script>
@endpush
