<div
    class="modal fade"
    id="modal-lista-precios"
    tabindex="-1"
    data-backdrop="static"
    role="dialog"
    aria-labelledby="modalListaPresciosLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form id="form-lista-precios">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" class="exclude-reset" />
            <input type="hidden" name="lista-precios_id" id="lista-precios_id" />
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalListaPreciosLabel">
                        <i class="fas fa-clipboard-check text-success mr-2"></i>
                        Nueva lista de precios
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Pestañas con íconos -->
                    <ul class="nav nav-tabs mb-3" id="lista-preciosTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-general">
                                <i class="fas fa-user me-1"></i>
                                General
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        @include('empresa.listaprecios.tabs.general')
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{--
    @push('js')
    <script>
    $(document).ready(function () {
    // Función utilitaria: agrega la opción al select y la selecciona
    function updateComboListaPrecios({ select, id, nombre }) {
    const $sel = select instanceof jQuery ? select : $(select);
    if (!$sel.length || !id) return;

    if (!$sel.find(`option[value="${id}"]`).length) {
    const opt = new Option(nombre, id, true, true);
    $sel.append(opt);
    } else {
    $sel.val(String(id));
    }

    if (
    $sel.data('select2') ||
    $sel.hasClass('select2') ||
    $sel.hasClass('select2bs4')
    ) {
    $sel.trigger('change.select2');
    } else {
    $sel.trigger('change');
    }
    }

    // Al cerrar el modal, limpiar todos los campos del formulario
    $('#modal-lista-precios').on('hidden.bs.modal', function () {
    const form = $(this).find('form')[0];
    form.reset();
    $(form).find('input[type=hidden]').not('[name="_token"]').val('');
    $(form).find('input[type=checkbox]').prop('checked', false);
    $(form).find('select').val('').trigger('change');
    });

    // Al mostrar el modal, activar la pestaña General
    $('#modal-lista-precios').on('show.bs.modal', function () {
    $(this).find('.nav-tabs a[href="#tab-general"]').tab('show');
    });

    // Abrir modal nuevo
    $('#btn-nuevo-lista-precios').on('click', function () {
    const form = $('#form-lista-precios')[0];
    form.reset();
    $('#form-lista-precios').find('input[type=hidden]').not('[name="_token"]').val('');
    $('#form-lista-precios').find('input[type=checkbox]').prop('checked', false);
    $('#form-lista-precios').find('select.select2').val('').trigger('change');
    $('#lista-precios_id').val('');
    $('#modalListaPreciosLabel').html(
    '<i class="fas fa-clipboard-check text-success mr-2"></i> Nueva lista de precios',
    );
    const hoy = new Date();
    const fecha = hoy
    .toLocaleDateString('es-EC')
    .split('/')
    .map((d) => d.padStart(2, '0'))
    .join('/');
    $('#modal-lista-precios').modal('show');
    });

    // Guardar o editar
    $('#form-lista-precios').on('submit', function (e) {
    e.preventDefault();
    const id = $('#lista-precios_id').val();
    const url = id
    ? `/empresa/lista-precios/${id}`
    : '{{ route('lista-precios.store') }}';
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
    updateComboListaPrecios(response.id, response.nombre);
    $('#modal-lista-precios').modal('hide');
    Swal.fire({
    icon: 'success',
    title: response.message || 'Lista de precios guardada correctamente.',
    toast: true,
    timer: 1500,
    position: 'top-end',
    showConfirmButton: false,
    });

    // ✅ Limpiar el formulario
    const form = $('#form-lista-precios')[0];
    form.reset();
    $('#form-lista-precios')
    .find('input[type=hidden]')
    .not('[name="_token"]')
    .val('');
    $('#form-lista-precios').find('input:checkbox').prop('checked', false);
    $('#form-lista-precios').find('select').val('').trigger('change');
    $('#form-lista-precios').find('.is-invalid').removeClass('is-invalid');
    $('#form-lista-precios').find('.error-message').remove();
    $('#tabla-lista-precios').DataTable().ajax.reload(null, false);
    },
    error: function (xhr) {
    if (xhr.status === 422) {
    // Errores de validación
    if (xhr.responseJSON?.errors) {
    const errors = xhr.responseJSON.errors;
    let messages = '';
    Object.keys(errors).forEach((key) => {
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
    },
    });
    });

    // Cargar edición
    $(document).on('click', '.editar-lista-precios', function () {
    const id = $(this).data('id');
    const url = `/empresa/lista-precios/${id}/show`;

    $.get(url, function (res) {
    const datos = res.listaprecios ?? {};

    $('#lista-precios_id').val(res.id);
    $('#nombre').val(res.nombre);
    $('#descripcion').val(res.descripcion);
    $('#estado').val(res.estado ?? 'activo');

    $('#modalListaPreciosLabel').html(
    '<i class="fas fa-edit text-warning mr-2"></i> Editar lista de precios',
    );
    $('#modal-lista-precios').modal('show');
    }).fail(function () {
    Swal.fire('Error', 'No se pudo cargar la lista de precios', 'error');
    });
    });

    // Eliminar vendedor
    $(document).on('click', '.eliminar-lista-precios', function () {
    const id = $(this).data('id');

    Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción eliminará la lista de precios.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    }).then((result) => {
    if (result.isConfirmed) {
    $.ajax({
    url: `/empresa/lista-precios/${id}`,
    type: 'DELETE',
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    success: function (response) {
    Swal.fire({
    icon: 'success',
    title: response.message,
    toast: true,
    timer: 1500,
    position: 'top-end',
    showConfirmButton: false,
    });
    $('#tabla-lista-precios').DataTable().ajax.reload(null, false);
    },
    error: function (xhr) {
    Swal.fire({
    icon: 'error',
    title: 'Error',
    text:
    xhr.responseJSON?.message ||
    'No se pudo eliminar la lista de precios',
    });
    },
    });
    }
    });
    });
    });
    </script>
    @endpush
--}}

@push('js')
    <script>
        $(document).ready(function () {
            // ==== Utilidad: agrega/selecciona la opción en el <select> indicado ====
            function updateComboListaPrecios({ select, id, nombre }) {
                const $sel = select instanceof jQuery ? select : $(select);
                if (!$sel.length || !id) return;

                // Agregar opción si no existe
                if (!$sel.find(`option[value="${id}"]`).length) {
                    const opt = new Option(nombre, id, true, true);
                    $sel.append(opt);
                } else {
                    $sel.val(String(id));
                }

                // Refrescar UI (Select2 o nativo)
                if (
                    $sel.data('select2') ||
                    $sel.hasClass('select2') ||
                    $sel.hasClass('select2bs4')
                ) {
                    $sel.trigger('change.select2');
                } else {
                    $sel.trigger('change');
                }
            }

            // ==== MODAL: limpiar al cerrar ====
            $('#modal-lista-precios').on('hidden.bs.modal', function () {
                const form = $(this).find('form')[0];
                if (form) {
                    form.reset();
                    $(form).find('input[type=hidden]').not('[name="_token"]').val('');
                    $(form).find('input[type=checkbox]').prop('checked', false);
                    $(form).find('select').val('').trigger('change');
                }
                // evita usar un select-target viejo
                $(this).removeData('select-target').removeData('origin');
            });

            // ==== MODAL: recordar a qué <select> actualizar (del botón que lo abre) ====
            $('#modal-lista-precios').on('show.bs.modal', function (e) {
                const $trigger = $(e.relatedTarget);
                const targetSel =
                    $trigger && $trigger.data('select-target')
                        ? $trigger.data('select-target')
                        : null;
                const origin = $trigger && $trigger.data('origin') ? $trigger.data('origin') : null;
                $(this).data('select-target', targetSel);
                $(this).data('origin', origin);

                // Activar pestaña "General" si existe
                $(this).find('.nav-tabs a[href="#tab-general"]').tab('show');
            });

            // ==== BOTÓN "nuevo" (por si abres el modal programáticamente) ====
            $('#btn-nuevo-lista-precios').on('click', function () {
                const form = $('#form-lista-precios')[0];
                if (form) {
                    form.reset();
                    $('#form-lista-precios')
                        .find('input[type=hidden]')
                        .not('[name="_token"]')
                        .val('');
                    $('#form-lista-precios').find('input[type=checkbox]').prop('checked', false);
                    $('#form-lista-precios')
                        .find('select.select2, select.select2bs4')
                        .val('')
                        .trigger('change');
                    $('#lista-precios_id').val('');
                }

                $('#modalListaPreciosLabel').html(
                    '<i class="fas fa-clipboard-check text-success mr-2"></i> Nueva lista de precios',
                );

                // Si quieres fijar un select por defecto cuando abres manual:
                // $('#modal-lista-precios').data('select-target', '#modal-cliente #id_lista_precios');

                $('#modal-lista-precios').modal('show');
            });

            // ==== GUARDAR / EDITAR ====
            $('#form-lista-precios').on('submit', function (e) {
                e.preventDefault();

                const id = $('#lista-precios_id').val();
                const url = id
                    ? `/empresa/lista-precios/${id}`
                    : '{{ route('lista-precios.store') }}';
                const method = 'POST'; // con FormData, usamos spoof para PUT

                const formData = new FormData(this);
                if (id) {
                    formData.append('_method', 'PUT');
                }

                // Deshabilitar submit mientras procesa
                const $btnSubmit = $(this).find('button[type="submit"]').prop('disabled', true);

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        // Esperamos { id, nombre, message }
                        const idNuevo = response?.id;
                        const nombreNuevo = response?.nombre;

                        // Determinar el SELECT objetivo (lo guardamos al abrir el modal)
                        const target =
                            $('#modal-lista-precios').data('select-target') || '#id_lista_precios';
                        updateComboListaPrecios({
                            select: target,
                            id: idNuevo,
                            nombre: nombreNuevo,
                        });

                        // Cerrar modal
                        $('#modal-lista-precios').modal('hide');

                        // Notificación
                        Swal.fire({
                            icon: 'success',
                            title: response?.message || 'Lista de precios guardada correctamente.',
                            toast: true,
                            timer: 1500,
                            position: 'top-end',
                            showConfirmButton: false,
                        });

                        // Limpiar formulario
                        const form = $('#form-lista-precios')[0];
                        if (form) {
                            form.reset();
                            $('#form-lista-precios')
                                .find('input[type=hidden]')
                                .not('[name="_token"]')
                                .val('');
                            $('#form-lista-precios').find('input:checkbox').prop('checked', false);
                            $('#form-lista-precios').find('select').val('').trigger('change');
                            $('#form-lista-precios').find('.is-invalid').removeClass('is-invalid');
                            $('#form-lista-precios').find('.error-message').remove();
                        }

                        // Recargar DataTable si existe en la vista actual
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tabla-lista-precios')) {
                            $('#tabla-lista-precios').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            if (xhr.responseJSON?.errors) {
                                const errors = xhr.responseJSON.errors;
                                let messages = '';
                                Object.keys(errors).forEach((key) => {
                                    messages += `<li>${errors[key][0]}</li>`;
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Errores de validación',
                                    html: `<ul class="text-left">${messages}</ul>`,
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
                    },
                    complete: function () {
                        $btnSubmit.prop('disabled', false);
                    },
                });
            });

            // ==== Cargar datos para edición ====
            $(document).on('click', '.editar-lista-precios', function () {
                const id = $(this).data('id');
                const url = `/empresa/lista-precios/${id}/show`;

                $.get(url, function (res) {
                    $('#lista-precios_id').val(res.id);
                    $('#nombre').val(res.nombre);
                    $('#descripcion').val(res.descripcion);
                    $('#estado').val(res.estado ?? 'activo');

                    $('#modalListaPreciosLabel').html(
                        '<i class="fas fa-edit text-warning mr-2"></i> Editar lista de precios',
                    );
                    $('#modal-lista-precios').modal('show');
                }).fail(function () {
                    Swal.fire('Error', 'No se pudo cargar la lista de precios', 'error');
                });
            });

            // ==== Eliminar lista de precios ====
            $(document).on('click', '.eliminar-lista-precios', function () {
                const id = $(this).data('id');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción eliminará la lista de precios.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/empresa/lista-precios/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    toast: true,
                                    timer: 1500,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                });
                                if (
                                    $.fn.DataTable &&
                                    $.fn.DataTable.isDataTable('#tabla-lista-precios')
                                ) {
                                    $('#tabla-lista-precios').DataTable().ajax.reload(null, false);
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text:
                                        xhr.responseJSON?.message ||
                                        'No se pudo eliminar la lista de precios',
                                });
                            },
                        });
                    }
                });
            });
        });
    </script>
@endpush
