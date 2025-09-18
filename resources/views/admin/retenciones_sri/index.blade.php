@extends('adminlte::page')

@section('title', 'Retenciones SRI')
@section('content')
<div class="card">
    <div
        id="est-header"
        class="d-flex justify-content-between align-items-center border-bottom mb-3 flex-wrap bg-white pb-2">
        <h4 class="text-primary mb-0">
            <i class="far fa-calendar-minus text-primary me-2"></i>
            Retenciones SRI
        </h4>
        <div class="d-flex gap-2">
            <button class="btn btn-success" id="btn-nueva-retencion">
                <i class="fas fa-plus"></i>
                Nueva Retención
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto">
            <table
                class="table-bordered table-striped nowrap table"
                id="tabla-retenciones"
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
                            Código
                        </th>
                        <th>Concepto</th>
                        <th>Observaciones</th>
                        <th>Porcentaje</th>
                        <th>Impuesto</th>
                        <th>Código ATS</th>
                        <th>Estado</th>
                        <th>Desde</th>
                        <th>Hasta</th>
                        <th>Acciones</th>
                    </tr>
                    <tr id="fila-filtros" class="filters" style="visibility: collapse">
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Código" />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Concepto" />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Observaciones" />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="%" />
                        </th>
                        <th>
                            <select class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="RENTA">RENTA</option>
                                <option value="IVA">IVA</option>
                            </select>
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Código ATS" />
                        </th>
                        <th>
                            <select class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Desde" />
                        </th>
                        <th>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Hasta" />
                        </th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear/Editar Retención -->
<div
    class="modal fade"
    id="modalRetencion"
    tabindex="-1"
    role="dialog"
    data-backdrop="static"
    data-keyboard="false"
    aria-labelledby="modalRetencionLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-retencion">
            @csrf
            <input type="hidden" name="id" id="retencion_id" />
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRetencionLabel">Nueva Retención</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="impuesto">Impuesto</label>
                            <select class="form-control" id="impuesto" name="impuesto" required>
                                <option value="RENTA">Renta</option>
                                <option value="IVA">IVA</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="codigo_retencion">Código Retención</label>
                            <input
                                type="text"
                                class="form-control"
                                id="codigo_retencion"
                                name="codigo_retencion"
                                required />
                        </div>

                        <div class="form-group col-md-3">
                            <label for="codigo_ats">Código ATS</label>
                            <input
                                type="text"
                                class="form-control"
                                id="codigo_ats"
                                name="codigo_ats"
                                required />
                        </div>
                        <div class="form-group col-md-3">
                            <label for="estado">Estado</label>
                            <select class="form-control" id="estado" name="estado">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-10">
                            <label for="concepto">Concepto</label>
                            <textarea
                                class="form-control"
                                id="concepto"
                                name="concepto"
                                rows="2"
                                maxlength="500"
                                required></textarea>
                            <small id="contador-concepto" class="form-text text-muted text-right">
                                0 / 500
                            </small>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="porcentaje">Porcentaje (%)</label>
                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                id="porcentaje"
                                name="porcentaje"
                                required />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="observaciones">Observaciones</label>
                            <textarea
                                class="form-control"
                                id="observaciones"
                                name="observaciones"
                                rows="1"
                                maxlength="255"></textarea>
                            <small
                                id="contador-observaciones"
                                class="form-text text-muted text-right">
                                0 / 255
                            </small>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="vigencia_desde">Vigencia Desde</label>
                            <input
                                type="date"
                                class="form-control"
                                id="vigencia_desde"
                                name="vigencia_desde"
                                required />
                        </div>

                        <div class="form-group col-md-3">
                            <label for="vigencia_hasta">Vigencia Hasta</label>
                            <input
                                type="date"
                                class="form-control"
                                id="vigencia_hasta"
                                name="vigencia_hasta"
                                required />
                        </div>
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
@stop

@section('js')

<script>
    $('#btn-toggle-filtros').on('click', function () {
        const filtros = $('#fila-filtros');
        const visible = filtros.css('visibility') !== 'collapse';
        filtros.css('visibility', visible ? 'collapse' : 'visible');
        $(this)
            .find('.texto-btn')
            .text(visible ? 'Mostrar filtros' : 'Ocultar filtros');
        // Si estás usando FixedHeader:
        if ($.fn.dataTable.FixedHeader) {
            $('#tabla-retenciones').DataTable().fixedHeader.adjust();
        }
    });

    // Abrir modal para nueva retención
    $('#btn-nueva-retencion').on('click', function () {
        $('#modalRetencionLabel').html(
            '<i class="fas fa-clipboard-check text-success mr-2"></i> Nueva Retención',
        );
        $('#form-retencion')[0].reset();
        $('#form-retencion .form-control').removeClass('is-invalid');
        $('#form-retencion .invalid-feedback').remove();
        $('#retencion_id').val('');
        $('#contador-observaciones').text('0 / 255');
        $('#contador-concepto').text('0 / 500');
        $('#modalRetencion').modal('show');
    });

    $('#concepto').on('input', function () {
        const max = 500;
        const length = $(this).val().length;

        $('#contador-concepto').text(`${length} / ${max}`);

        if (length > max) {
            $('#contador-concepto').addClass('text-danger');
        } else if (length > 510) {
            $('#contador-concepto').removeClass('text-danger').addClass('text-warning');
        } else {
            $('#contador-concepto').removeClass('text-warning text-danger');
        }
    });

    $('#observaciones').on('input', function () {
        const max = 255;
        const length = $(this).val().length;

        $('#contador-observaciones').text(`${length} / ${max}`);

        if (length > max) {
            $('#contador-observaciones').addClass('text-danger');
        } else if (length > 230) {
            $('#contador-observaciones').removeClass('text-danger').addClass('text-warning');
        } else {
            $('#contador-observaciones').removeClass('text-warning text-danger');
        }
    });

    // Cargar datos en el modal para editar
    $(document).on('click', '.editar', function () {
        const id = $(this).data('id');
        $.get(`/retenciones-sri/${id}`, function (data) {
            $('#modalRetencionLabel').html(
                '<i class="fas fa-edit text-warning mr-2"></i> Editar Retención',
            );
            $('#retencion_id').val(data.id);
            $('#codigo_retencion').val(data.codigo_retencion);
            $('#concepto').val(data.concepto);
            $('#observaciones').val(data.observaciones);
            $('#porcentaje').val(data.porcentaje);
            $('#impuesto').val(data.impuesto);
            $('#codigo_ats').val(data.codigo_ats);
            $('#estado').val(data.estado);
            $('#vigencia_desde').val(data.vigencia_desde);
            $('#vigencia_hasta').val(data.vigencia_hasta);
            $('#modalRetencion').modal('show');
        });
    });

    $(function () {
        let tabla = $('#tabla-retenciones').DataTable({
            dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
            processing: false,
            serverSide: true,
            fixedHeader: true,
            autoWidth: true,
            ajax: {
                url: '{{ route('retenciones.data') }}',
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
                { data: 'codigo_retencion' },
                { data: 'concepto' },
                { data: 'observaciones' },
                { data: 'porcentaje' },
                { data: 'impuesto' },
                { data: 'codigo_ats' },
                { data: 'estado' },
                { data: 'vigencia_desde' },
                { data: 'vigencia_hasta' },
                { data: 'acciones', orderable: false, searchable: false },
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json',
            },
        });

        // Detectar cambios en los filtros y recargar
        $('#tabla-retenciones thead').on(
            'input change',
            '.filters input, .filters select',
            function () {
                tabla.ajax.reload();
            },
        );
    });

    $('#form-retencion').on('submit', function (e) {
        e.preventDefault();
        const id = $('#retencion_id').val();
        const url = id ? `/retenciones-sri/update/${id}` : '{{ route('retenciones.store') }}';
        const method = id ? 'PUT' : 'POST';

        const desde = $('#vigencia_desde').val();
        const hasta = $('#vigencia_hasta').val();

        if (!desde || !hasta) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Fechas requeridas',
                text: 'Debes completar ambos campos de vigencia.',
            });
            return false;
        }

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function (res) {
                $('#modalRetencion').modal('hide');
                $('#tabla-retenciones').DataTable().ajax.reload();

                // Limpiar formulario
                $('#form-retencion')[0].reset();
                $('#form-retencion .form-control').removeClass('is-invalid');
                $('#form-retencion .invalid-feedback').remove();
                $('#contador-observaciones').text('0 / 255');
                $('#contador-concepto').text('0 / 500');

                Swal.fire({
                    icon: 'success',
                    title: id
                        ? 'Concepto de retención actualizado'
                        : 'Concepto de retención creado',
                    text: 'La información ha sido guardada correctamente.',
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    let errores = xhr.responseJSON.errors;

                    $('#form-retencion .form-control').removeClass('is-invalid');
                    $('#form-retencion .invalid-feedback').remove();

                    $.each(errores, function (campo, mensajes) {
                        let input = $(`#form-retencion [name="${campo}"]`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback">${mensajes[0]}</div>`);
                    });

                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos inválidos',
                        text: 'Corrige los errores resaltados en el formulario.',
                    });
                } else {
                    //console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error inesperado',
                        text: 'No se pudo guardar la retención, completa los campos requeridos e intenta nuevamente.',
                    });
                }
            },
        });
    });
</script>
@stop
