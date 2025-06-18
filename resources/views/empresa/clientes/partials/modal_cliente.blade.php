<!-- resources/views/partials/modal_cliente.blade.php -->
<div class="modal fade" id="modal-cliente" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-cliente">
            @csrf
            <input type="hidden" name="cliente_id" id="cliente_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClienteLabel">Nuevo Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="form-group col-md-4">
                        <label>Tipo de Identificación</label>
                        <select name="tipo_identificacion" id="tipo_identificacion" class="form-control">
                            <option value="04">RUC</option>
                            <option value="05">Cédula</option>
                            <option value="06">Pasaporte</option>
                            <option value="07">Consumidor final</option>
                            <option value="08">Identificación del exterior</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Número Identificación</label>
                        <input type="text" name="numero_identificacion" id="numero_identificacion" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Razón social / Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" maxlength="10">
                    </div>
                    <div class="form-group col-md-9">
                        <label>Email(s)</label>
                        <input type="text" name="email" id="email" class="form-control" placeholder="Separar múltiples emails con coma">
                    </div>
                    <div class="form-group col-md-12">
                        <label>Dirección</label>
                        <input type="text" name="direccion" id="direccion" class="form-control">
                    </div>
                    <div class="form-group col-md-8">
                        <label>Vendedor</label>
                        <select name="id_vendedor" id="id_vendedor" class="form-control"></select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Plazo crédito (días)</label>
                        <input type="number" name="plazo_credito" id="plazo_credito" class="form-control" value="0">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Provincia</label>
                        <input type="text" name="provincia" id="provincia" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Ciudad</label>
                        <input type="text" name="ciudad" id="ciudad" class="form-control">
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

    $('#btn-nuevo-cliente').on('click', function () {
        $('#form-cliente')[0].reset();
        $('#cliente_id').val('');
        $('#modalClienteLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo Cliente');
        $('#modal-cliente').modal('show');
    });

$('#numero_identificacion').on('change', function () {
    const numero_identificacion = $(this).val();
    const tipo_identificacion = $('#tipo_identificacion').val();

    const esCedulaValida = tipo_identificacion === '05' && numero_identificacion.length === 10;
    const esRucValido = tipo_identificacion === '04' && numero_identificacion.length === 13;

    if (esCedulaValida || esRucValido) {
        // 🔍 Buscar en la base de datos local primero
        $.get('{{ route("clientes.buscarPorIdentificacion") }}', { numero_identificacion }, function (data) {
            if (data.encontrado) {
                const p = data.persona;
                $('#nombre').val(p.nombre ?? '');
                $('#estado').val(p.estado ? '1' : '0');
                $('#provincia').val(p.provincia ?? '');
                $('#ciudad').val(p.ciudad ?? '');
                $('#direccion').val(p.direccion ?? '');
                $('#email').val(p.email ?? '');
            } else {
                // No encontrado localmente, consultar API externa
                Swal.fire({
                    title: 'Consultando desde SRI...',
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
                            const contribuyente = response.data?.datosContribuyente?.[0];
                            const establecimientos = response.data?.establecimientos ?? [];
                            if (contribuyente) {
                                $('#nombre').val(contribuyente.razonSocial ?? '');
                                $('#estado').val(contribuyente.estadoContribuyenteRuc === 'ACTIVO' ? '1' : '0');
                            }
                            const matriz = establecimientos.find(est => est.matriz === 'SI');
                            if (matriz?.direccionCompleta) {
                                const partes = matriz.direccionCompleta.split(' / ');
                                $('#provincia').val(partes[0] ?? '');
                                $('#ciudad').val(partes[1] ?? '');
                                $('#direccion').val(partes[3] ?? '');
                            }
                        }

                        if (esCedulaValida) {
                            const contribuyente = response.data;
                            $('#nombre').val(contribuyente.nombreCompleto ?? '');
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

});


// Guardar o editar
$('#form-cliente').on('submit', function (e) {
    e.preventDefault();
    const id = $('#cliente_id').val();
    const url = id ? `/empresa/clientes/${id}` : '{{ route("clientes.store") }}';
    const method = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        method: method,
        data: $(this).serialize(),
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

$(document).on('click', '.editar-cliente', function () {
    const id = $(this).data('id');
    const url = '{{ route("clientes.edit", ":id") }}'.replace(':id', id);
    $.get(url, function (res) {
        $('#cliente_id').val(res.id);
        $('#tipo_identificacion').val(res.tipo_identificacion);
        $('#numero_identificacion').val(res.numero_identificacion);
        $('#nombre').val(res.nombre);
        $('#telefono').val(res.telefono);
        $('#email').val(res.email);
        $('#direccion').val(res.direccion);
        $('#id_vendedor').val(res.id_vendedor).trigger('change');
        $('#plazo_credito').val(res.plazo_credito);
        $('#provincia').val(res.provincia);
        $('#ciudad').val(res.ciudad);
        $('#estado').val(res.estado ? 1 : 0);
        $('#modalClienteLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar Cliente');
        $('#modal-cliente').modal('show');
    }).fail(function () {
        Swal.fire('Error', 'No se pudo cargar el cliente', 'error');
    });
});


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

</script>
@endpush
