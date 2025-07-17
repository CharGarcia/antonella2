
<div class="modal fade" id="modal-comprador" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modalCompradorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-comprador">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" class="exclude-reset">
            <input type="hidden" name="comprador_id" id="comprador_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCompradorLabel">Gestión de Comprador</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Pestañas -->
                    <ul class="nav nav-tabs mb-3" id="compradorTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-general-comprador">
                                <i class="fas fa-user me-1"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-comercial-comprador">
                                <i class="fas fa-briefcase me-1"></i> Comercial
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        @include('empresa.compradores.tabs.general')
                        @include('empresa.compradores.tabs.comercial')
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

    $('#modal-comprador').on('hidden.bs.modal', function () {
        const form = $(this).find('form')[0];
        form.reset();
        $(form).find('input[type=hidden]').not('[name="_token"]').val('');
        $(form).find('input[type=checkbox]').prop('checked', false);
        $(form).find('select').val('').trigger('change');
    });

    $('#modal-comprador').on('show.bs.modal', function () {
        $(this).find('.nav-tabs a[href="#tab-general-comprador"]').tab('show');
    });

    $('#btn-nuevo-comprador').on('click', function () {
        const form = $('#form-comprador')[0];
        form.reset();

        $('#form-comprador').find('input[type=hidden]').not('[name="_token"]').val('');
        $('#form-comprador').find('input[type=checkbox]').prop('checked', false);
        $('#form-comprador').find('select.select2').val('').trigger('change');
        $('#comprador_id').val('');
        $('#modalCompradorLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo Comprador');

        const hoy = new Date();
        const fecha = hoy.toLocaleDateString('es-EC').split('/').map(d => d.padStart(2, '0')).join('/');
        $('#inicio_relacion').val(fecha);
        $('#modal-comprador').modal('show');
    });

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
                    $('#estado').val(p.datos_comprador?.estado ?? 'activo');
                    $('#pais').val(p.pais ?? '');
                    $('#provincia').val(p.provincia ?? '');
                    $('#ciudad').val(p.ciudad ?? '');
                    $('#zona').val(p.zona ?? '');
                    $('#direccion').val(p.direccion ?? '');
                    $('#email').val(p.email ?? '');
                }
            });
        }
    });

    $('#form-comprador').on('submit', function (e) {
        e.preventDefault();

        const id = $('#comprador_id').val();
        const url = id
            ? `/empresa/compradores/${id}`
            : '{{ route("compradores.store") }}';

        const formData = new FormData(this);
        if (id) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $('#modal-comprador').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: response.message || 'Comprador guardado correctamente.',
                    toast: true,
                    timer: 1500,
                    position: 'top-end',
                    showConfirmButton: false
                });

                $('#form-comprador')[0].reset();
                $('#tabla-compradores').DataTable().ajax.reload(null, false);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors;
                    let messages = '';
                    Object.keys(errors).forEach(key => {
                        messages += `<li>${errors[key][0]}</li>`;
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Errores de validación',
                        html: `<ul class="text-left">${messages}</ul>`,
                    });
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

    $(document).on('click', '.editar-comprador', function () {
        const id = $(this).data('id');
        const url = `/empresa/compradores/${id}/edit`;

        $.get(url, function (res) {
            const datos = res.datos_comprador ?? {};

            $('#comprador_id').val(res.id);
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

            $('#codigo_interno').val(datos.codigo_interno ?? '');
            $('#perfil').val(datos.perfil ?? '');
            $('#fecha_registro').val(datos.fecha_registro ?? '');
            $('#zona').val(datos.zona ?? '');
            $('#inicio_relacion').val(datos.inicio_relacion_formatted ?? '');
            $('#informacion_adicional').val(datos.informacion_adicional ?? '');

            $('#modalCompradorLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar Comprador');
            $('#modal-comprador').modal('show');
        }).fail(function () {
            Swal.fire('Error', 'No se pudo cargar el comprador', 'error');
        });
    });

    $(document).on('click', '.eliminar-comprador', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el comprador.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/empresa/compradores/${id}`,
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
                        $('#tabla-compradores').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'No se pudo eliminar el comprador'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush
