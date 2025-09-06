@extends('adminlte::page')

@section('title', 'Formas Pago SRI')

@section('content_header')
    <h1>Gestión de Formas de Pago SRI</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-end align-items-center mb-3">
            <button class="btn btn-success" id="btn-nueva-formas_pago_sri">
                <i class="fas fa-plus"></i> Nueva
            </button>
        </div>

        <div class="table-responsive" style="max-height: 600px;">
            <table class="table table-bordered table-striped nowrap" id="tabla-formas_pago_sri" style="width:100%;">
                <thead class="table-primary">
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


    <div class="modal fade" id="modal-formas_pago_sri" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <form id="form-formas_pago_sri" class="modal-content">
            @csrf
            <input type="hidden" name="id" id="id">
            <div class="modal-header">
                <h5 class="modal-title" id="modalformas_pago_sriLabel">Formas de pago SRI</h5>
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
/*         $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
 */

        let tabla = $('#tabla-formas_pago_sri').DataTable({
        dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
        processing: false,
        serverSide: true,
        fixedHeader: true,
        autoWidth: true,
            ajax: {
                url: '{{ route("formas_pago_sri.data") }}',
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
                { data: 'estado'},
                { data: 'acciones', orderable: false, searchable: false },
            ],
            language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });


        $('#btn-nueva-formas_pago_sri').click(function () {
            $('#form-formas_pago_sri')[0].reset();
            $('#id').val('');
            $('#modalformas_pago_sriLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nueva forma pago');
            $('#modal-formas_pago_sri').modal('show');
        });


        //para guardar o editar
        $('#form-formas_pago_sri').on('submit', function (e) {
            e.preventDefault();
            let id = $('#id').val();
            let method = id ? 'PUT' : 'POST';
            let url = id ? `/formas_pago_sri/update/${id}` : '{{ route("formas_pago_sri.store") }}';

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: res => {
                    $('#modal-formas_pago_sri').modal('hide');
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
    $(document).on('click', '.editar-formas_pago_sri', function () {
        const id = $(this).data('id');
        $.get(`/formas_pago_sri/${id}`, function (data) {
            $('#id').val(data.id);
            $('#codigo').val(data.codigo);
            $('#descripcion').val(data.descripcion);
            $('#estado').val(data.estado);
            $('#modalformas_pago_sriLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar forma de pago');
            $('#modal-formas_pago_sri').modal('show');
        });
    });

    });
</script>
@stop
