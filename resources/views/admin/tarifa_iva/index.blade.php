@extends('adminlte::page')

@section('title', 'Tarifa IVA')

@section('content_header')
    <h1>Gestión de Tarifas de IVA</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-end align-items-center mb-3">
            <button class="btn btn-success" id="btn-nueva-tarifa_iva">
                <i class="fas fa-plus"></i> Nueva Tarifa IVA
            </button>
        </div>

        <div class="table-responsive" style="max-height: 600px;">
            <table class="table table-bordered table-striped nowrap" id="tabla-tarifa_iva" style="width:100%;">
                <thead class="table-primary">
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Porcentaje</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


    <div class="modal fade" id="modal-tarifa_iva" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <form id="form-tarifa_iva" class="modal-content">
            @csrf
            <input type="hidden" name="id" id="id">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTarifaIvaLabel">Tarifa IVA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body row">

                <div class="form-group col-md-12 mb-2">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" name="descripcion" id="descripcion" class="form-control" required>
                </div>
                <div class="form-group col-md-4 mb-2">
                    <label for="codigo" class="form-label">Código</label>
                    <input type="text" name="codigo" id="codigo" class="form-control" required>
                </div>
                <div class="form-group col-md-4 mb-2">
                    <label for="porcentaje" class="form-label">Porcentaje</label>
                    <input type="number" name="porcentaje" id="porcentaje" class="form-control" step="0.01" required>
                </div>
                <div class="form-group col-md-4 mb-2">
                    <label class="form-label" for="estado">Estado</label>
                    <select class="form-control" id="estado" name="estado" required>
                        <option value="activo" Selected>Activo</option>
                        <option value="inactivo">Inactivo</option>
                      </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

@stop

@section('js')
<script>
    $(function () {

//para eliminar un registro debe tener esta opcion para que permita eliminar
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        let tabla = $('#tabla-tarifa_iva').DataTable({
        dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
        processing: false,
        serverSide: true,
        fixedHeader: true,
        autoWidth: true,
            ajax: {
                url: '{{ route("tarifa_iva.data") }}',
                data: function (d) {
                    $('.filters th').each(function (i) {
                        const input = $(this).find('input, select');
                        if (input.length) {
                            d['columns[' + i + '][search][value]'] = input.val();
                        }
                    });
                }
            },
            columns: [
                { data: 'codigo', name: 'codigo' },
                { data: 'descripcion', name: 'descripcion' },
                { data: 'porcentaje', name: 'porcentaje' },
                { data: 'estado'},
                { data: 'acciones', orderable: false, searchable: false },
            ],
            language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });


        $('#btn-nueva-tarifa_iva').click(function () {
            $('#form-tarifa_iva')[0].reset();
            $('#id').val('');
            $('#modalTarifaIvaLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nueva tarifa IVA');
            $('#modal-tarifa_iva').modal('show');
        });


        //para guardar o editar
        $('#form-tarifa_iva').on('submit', function (e) {
            e.preventDefault();
            let id = $('#id').val();
            let method = id ? 'PUT' : 'POST';
            let url = id ? `/tarifa_iva/update/${id}` : '{{ route("tarifa_iva.store") }}';

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: res => {
                    $('#modal-tarifa_iva').modal('hide');
                    tabla.ajax.reload();
                    Swal.fire('Éxito', res.message, 'success');
                },
                error: err => {
                    let errors = err.responseJSON.errors;
                    let msg = Object.values(errors).map(e => `<p>${e}</p>`).join('');
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

            // Editar
    $(document).on('click', '.editar-tarifa_iva', function () {
        const id = $(this).data('id');
        $.get(`/tarifa_iva/${id}`, function (data) {
            $('#id').val(data.id);
            $('#codigo').val(data.codigo);
            $('#descripcion').val(data.descripcion);
            $('#porcentaje').val(data.porcentaje);
            $('#estado').val(data.estado);
            $('#modalTarifaIvaLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar Tarifa IVA');
            $('#modal-tarifa_iva').modal('show');
        });
    });

        $('#tabla-tarifa_iva').on('click', '.eliminar-tarifa_iva', function () {
            let id = $(this).data('id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/tarifa_iva/${id}`,
                        method: 'DELETE',
                        success: res => {
                            tabla.ajax.reload();
                            Swal.fire('Eliminado', res.message, 'success');
                        }
                    });
                }
            });
        });

    });
</script>
@stop
