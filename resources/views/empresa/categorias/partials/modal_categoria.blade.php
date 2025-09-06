<div class="modal fade" id="modal-categoria" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form id="form-categoria">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" class="exclude-reset">
            <input type="hidden" name="categoria_id" id="categoria_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCategoriaLabel">Gestión de Categorías</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Pestañas con íconos -->
                    <ul class="nav nav-tabs mb-3" id="categoriaTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-general">
                                <i class="fas fa-user me-1"></i> General
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        @include('empresa.categorias.tabs.general')
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
    // Al cerrar el modal, limpiar todos los campos del formulario
    $('#modal-categoria').on('hidden.bs.modal', function () {
        const form = $(this).find('form')[0];
        form.reset();
        $(form).find('input[type=hidden]').not('[name="_token"]').val('');
        $(form).find('input[type=checkbox]').prop('checked', false);
        $(form).find('select').val('').trigger('change');
    });

    // Al mostrar el modal, activar la pestaña General
    $('#modal-categoria').on('show.bs.modal', function () {
        $(this).find('.nav-tabs a[href="#tab-general"]').tab('show');
    });


    // Abrir modal nuevo categoria
    $('#btn-nuevo-categoria').on('click', function () {
        const form = $('#form-categoria')[0];
        form.reset();
        $('#form-categoria').find('input[type=hidden]').not('[name="_token"]').val('');
        $('#form-categoria').find('input[type=checkbox]').prop('checked', false);
        $('#form-categoria').find('select.select2').val('').trigger('change');
        $('#categoria_id').val('');
        $('#modalCategoriaLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nueva Categoría');
        const hoy = new Date();
        const fecha = hoy.toLocaleDateString('es-EC').split('/').map(d => d.padStart(2, '0')).join('/');
        $('#modal-categoria').modal('show');
    });

      // Guardar o editar vendedor
$('#form-categoria').on('submit', function (e) {
    e.preventDefault();
    const id = $('#categoria_id').val();
    const url = id
        ? `/empresa/categorias/${id}`
        : '{{ route("categorias.store") }}';

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
            $('#modal-categoria').modal('hide');
            Swal.fire({
                icon: 'success',
                title: response.message || 'Categoría guardada correctamente.',
                toast: true,
                timer: 1500,
                position: 'top-end',
                showConfirmButton: false
            });

            // ✅ Limpiar el formulario
            const form = $('#form-categoria')[0];
            form.reset();
            $('#form-categoria').find('input[type=hidden]').not('[name="_token"]').val('');
            $('#form-categoria').find('input:checkbox').prop('checked', false);
            $('#form-categoria').find('select').val('').trigger('change');
            $('#form-categoria').find('.is-invalid').removeClass('is-invalid');
            $('#form-categoria').find('.error-message').remove();
            $('#tabla-categorias').DataTable().ajax.reload(null, false);
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

    // Cargar categoria en edición
$(document).on('click', '.editar-categoria', function () {
    const id = $(this).data('id');
    const url = `/empresa/categorias/${id}/show`;

    $.get(url, function (res) {
        const datos = res.categoria ?? {};

        $('#categoria_id').val(res.id);
        $('#nombre').val(res.nombre);
        $('#descripcion').val(res.descripcion);
        $('#status').val(res.status ?? 'activo');

        $('#modalCategoriaLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar Categoría');
        $('#modal-categoria').modal('show');
    }).fail(function () {
        Swal.fire('Error', 'No se pudo cargar el categoría', 'error');
    });
});

    // Eliminar vendedor
    $(document).on('click', '.eliminar-categoria', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará la categoría.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/empresa/categorias/${id}`,
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
                        $('#tabla-categorias').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'No se pudo eliminar la categoría'
                        });
                    }
                });
            }
        });
    });
});

</script>
@endpush
