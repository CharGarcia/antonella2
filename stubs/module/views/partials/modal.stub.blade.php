<div class="modal fade" id="modal-{{ moduleLower }}" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal{{ module }}Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form id="form-{{ moduleLower }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" class="exclude-reset">
            <input type="hidden" name="{{ moduleLower }}_id" id="{{ moduleLower }}_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal{{ module }}Label">Gestión de {{ plural }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Pestañas con íconos -->
                    <ul class="nav nav-tabs mb-3" id="{{ moduleLower }}Tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-general">
                                <i class="fas fa-user me-1"></i> General
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        @include('empresa.{{ plural }}.tabs.general')
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
    $('#modal-{{ moduleLower }}').on('hidden.bs.modal', function () {
        const form = $(this).find('form')[0];
        form.reset();
        $(form).find('input[type=hidden]').not('[name="_token"]').val('');
        $(form).find('input[type=checkbox]').prop('checked', false);
        $(form).find('select').val('').trigger('change');
    });

    // Al mostrar el modal, activar la pestaña General
    $('#modal-{{ moduleLower }}').on('show.bs.modal', function () {
        $(this).find('.nav-tabs a[href="#tab-general"]').tab('show');
    });


    // Abrir modal nuevo
    $('#btn-nuevo-{{ moduleLower }}').on('click', function () {
        const form = $('#form-{{ moduleLower }}')[0];
        form.reset();
        $('#form-{{ moduleLower }}').find('input[type=hidden]').not('[name="_token"]').val('');
        $('#form-{{ moduleLower }}').find('input[type=checkbox]').prop('checked', false);
        $('#form-{{ moduleLower }}').find('select.select2').val('').trigger('change');
        $('#{{ moduleLower }}_id').val('');
        $('#modal{{ module }}Label').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nueva {{ module }}');
        const hoy = new Date();
        const fecha = hoy.toLocaleDateString('es-EC').split('/').map(d => d.padStart(2, '0')).join('/');
        $('#modal-{{ moduleLower }}').modal('show');
    });

      // Guardar o editar
$('#form-{{ moduleLower }}').on('submit', function (e) {
    e.preventDefault();
    const id = $('#{{ moduleLower }}_id').val();
    const url = id
        ? `/empresa/{{ plural }}/${id}`
        : '{{ route("{{ plural }}.store") }}';

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
            $('#modal-{{ moduleLower }}').modal('hide');
            Swal.fire({
                icon: 'success',
                title: response.message || '{{ module }} guardada correctamente.',
                toast: true,
                timer: 1500,
                position: 'top-end',
                showConfirmButton: false
            });

            // ✅ Limpiar el formulario
            const form = $('#form-{{ moduleLower }}')[0];
            form.reset();
            $('#form-{{ moduleLower }}').find('input[type=hidden]').not('[name="_token"]').val('');
            $('#form-{{ moduleLower }}').find('input:checkbox').prop('checked', false);
            $('#form-{{ moduleLower }}').find('select').val('').trigger('change');
            $('#form-{{ moduleLower }}').find('.is-invalid').removeClass('is-invalid');
            $('#form-{{ moduleLower }}').find('.error-message').remove();
            $('#tabla-{{ plural }}').DataTable().ajax.reload(null, false);
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

    // Cargar para edición
$(document).on('click', '.editar-{{ moduleLower }}', function () {
    const id  = $(this).data('id');
    const url = `/empresa/{{ plural }}/${id}/show`; // si usas REST puro sería: /empresa/{{ plural }}/${id}

    $.get(url)
      .done(function (res) {
          // Normaliza el payload: puede venir como { {{ moduleLower }}: {...} } o directamente {...}
          const data = res?.{{ moduleLower }} ?? res ?? {};

          // Intenta setear un hidden para el ID (si existe con alguno de estos selectores)
          $('[name="id"], #{{ moduleLower }}_id, #{{ moduleLower }}-id, #{{ moduleLower }}id, #id').val(data.id ?? id);

          // === Bloque generado por tu MakeModule.php ===
          // El generador inserta líneas concretas para cada field, ej:
          // $('[name="nombre"]').val(data.nombre ?? '');
          // $('[name="descripcion"]').val(data.descripcion ?? '');
          // $('[name="status"]').val(data.status ?? 'activo');
          // Mantén EXACTAMENTE este marcador:
          {{ fillFormForEdit }}
          // === Fin bloque generado ===

          // === Relleno genérico (por si agregas campos nuevos sin regenerar JS) ===
          Object.entries(data).forEach(([field, value]) => {
              const $targets = $(
                  `[name="${field}"], #${field}, [data-field="${field}"]`
              );
              if (!$targets.length) return;

              $targets.each(function () {
                  const $el = $(this);
                  if ($el.is(':checkbox')) {
                      // Para booleanos: 1/true → checked
                      $el.prop('checked', Boolean(Number(value) || value === true || value === '1' || value === 'true'));
                  } else if ($el.is(':radio')) {
                      $(`input[name="${field}"][value="${value}"]`).prop('checked', true);
                  } else if ($el.is('select')) {
                      $el.val(value).trigger('change');
                  } else {
                      $el.val(value ?? '');
                  }
              });
          });

          // Default para status si no vino en data
          if ($('[name="status"]').length && (data.status == null || data.status === '')) {
              $('[name="status"]').val('activo');
          }

          // Título y apertura de modal
          $('#modal{{ module }}Label').html('<i class="fas fa-edit text-warning mr-2"></i> Editar {{ module }}');
          $('#modal-{{ moduleLower }}').modal('show');
      })
      .fail(function () {
          Swal.fire('Error', 'No se pudo cargar', 'error');
      });
});


    // Eliminar vendedor
    $(document).on('click', '.eliminar-{{ moduleLower }}', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el registro.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/empresa/{{ plural }}/${id}`,
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
                        $('#tabla-{{ plural }}').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'No se pudo eliminar el registro'
                        });
                    }
                });
            }
        });
    });
});

</script>
@endpush
