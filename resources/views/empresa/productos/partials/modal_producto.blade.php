<div class="modal fade" id="modal-producto" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-producto">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" class="exclude-reset">
            <input type="hidden" name="producto_id" id="producto_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProductoLabel">Gestión de productos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Pestañas con íconos -->
                    <ul class="nav nav-tabs mb-3" id="productoTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-general">
                                <i class="fas fa-user me-1"></i> General
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        @include('empresa.productos.tabs.general')
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
    $('#modal-producto').on('hidden.bs.modal', function () {
        const form = $(this).find('form')[0];
        form.reset();
        $(form).find('input[type=hidden]').not('[name="_token"]').val('');
        $(form).find('input[type=checkbox]').prop('checked', false);
        $(form).find('select').val('').trigger('change');
    });

    // Al mostrar el modal, activar la pestaña General
    $('#modal-producto').on('show.bs.modal', function () {
        $(this).find('.nav-tabs a[href="#tab-general"]').tab('show');
    });


    // Abrir modal nuevo
    $('#btn-nuevo-producto').on('click', function () {
        const form = $('#form-producto')[0];
        form.reset();
        $('#form-producto').find('input[type=hidden]').not('[name="_token"]').val('');
        $('#form-producto').find('input[type=checkbox]').prop('checked', false);
        $('#form-producto').find('select.select2').val('').trigger('change');
        $('#producto_id').val('');
        $('#modalProductoLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo Producto');
        const hoy = new Date();
        const fecha = hoy.toLocaleDateString('es-EC').split('/').map(d => d.padStart(2, '0')).join('/');
       // Cargar automáticamente el siguiente código
        $.get('{{ route("productos.siguiente-codigo") }}', function (res) {
            if (res.codigo) {
                $('[name="codigo"]').val(res.codigo);
            }
        });
        $('#modal-producto').modal('show');
    });


      // Guardar o editar
$('#form-producto').on('submit', function (e) {
    e.preventDefault();
    const id = $('#producto_id').val();
    const url = id
        ? `/empresa/productos/${id}`
        : '{{ route("productos.store") }}';

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
            $('#modal-producto').modal('hide');
            Swal.fire({
                icon: 'success',
                title: response.message || 'Producto guardado correctamente.',
                toast: true,
                timer: 1500,
                position: 'top-end',
                showConfirmButton: false
            });

            // ✅ Limpiar el formulario
            const form = $('#form-producto')[0];
            form.reset();
            $('#form-producto').find('input[type=hidden]').not('[name="_token"]').val('');
            $('#form-producto').find('input:checkbox').prop('checked', false);
            $('#form-producto').find('select').val('').trigger('change');
            $('#form-producto').find('.is-invalid').removeClass('is-invalid');
            $('#form-producto').find('.error-message').remove();
            $('#tabla-productos').DataTable().ajax.reload(null, false);
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
$(document).on('click', '.editar-producto', function () {
    const id  = $(this).data('id');
    const url = `/empresa/productos/${id}/show`; // si usas REST puro sería: /empresa/productos/${id}

    $.get(url)
      .done(function (res) {
          // Normaliza el payload: puede venir como { producto: {...} } o directamente {...}
          const data = res?.producto ?? res ?? {};
          // Intenta setear un hidden para el ID (si existe con alguno de estos selectores)
          $('[name="producto_id"]').val(data.id);
          $('[name="codigo"]').val(data.codigo ?? '');
            $('[name="descripcion"]').val(data.descripcion ?? '');
            $('[name="tipo_id"]').val(data.tipo_id ?? '');
            $('[name="tarifa_iva_id"]').val(data.tarifa_iva_id ?? '');
            $('[name="precio_base"]').val(data.precio_base ?? '');
            $('[name="estado"]').val(String(data.estado ?? 'activo'));

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

          // Default para estado si no vino en data
          if ($('[name="estado"]').length && (data.estado == null || data.estado === '')) {
              $('[name="estado"]').val('activo');
          }

          // Título y apertura de modal
          $('#modalProductoLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar Producto');
          $('#modal-producto').modal('show');
      })
      .fail(function () {
          Swal.fire('Error', 'No se pudo cargar', 'error');
      });
});


    // Eliminar vendedor
    $(document).on('click', '.eliminar-producto', function () {
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
                    url: `/empresa/productos/${id}`,
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
                        $('#tabla-productos').DataTable().ajax.reload(null, false);
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


//para que se calcule el valor de un producto mas el iva
        function calcularPVP() {
            const precioBase = parseFloat($('#precio_base').val()) || 0;
            const selectIva = $('#tarifa_iva_id');
            const selectedOption = selectIva.find('option:selected');
            let porcentajeIva = parseFloat(selectedOption.data('porcentaje')) || 0;

            const pvp = precioBase + (precioBase * porcentajeIva / 100);
            $('#pvp').val(pvp.toFixed(2));
        }

        // Escuchamos cambios en precio base o tarifa iva
        $('#precio_base, #tarifa_iva_id').on('input change', calcularPVP);

        // Cargar porcentaje como data-attribute si no se ha hecho en backend
        @foreach($tarifaIva as $codigo => $descripcion)
            $('#tarifa_iva_id option[value="{{ $codigo }}"]').attr('data-porcentaje', @json(\App\Models\Admin\TarifaIva::where('codigo', $codigo)->value('porcentaje')));
        @endforeach



        function obtenerPorcentajeIVA() {
            const selectedOption = $('#tarifa_iva_id').find('option:selected');
            return parseFloat(selectedOption.data('porcentaje')) || 0;
        }

        function actualizarPVP() {
            const base = parseFloat($('#precio_base').val()) || 0;
            const iva = obtenerPorcentajeIVA();
            const pvp = base + (base * (iva / 100));
            $('#pvp').val(pvp.toFixed(2));
        }

        function actualizarBaseDesdePVP() {
            const pvp = parseFloat($('#pvp').val()) || 0;
            const iva = obtenerPorcentajeIVA();
            const base = pvp / (1 + (iva / 100));
            $('#precio_base').val(base.toFixed(2));
        }

        // Escucha cuando cambian base o tarifa IVA
        $('#precio_base, #tarifa_iva_id').on('input change', actualizarPVP);

        // Escucha cuando cambia el PVP para actualizar base
        $('#pvp').on('input', actualizarBaseDesdePVP);

});

</script>
@endpush


