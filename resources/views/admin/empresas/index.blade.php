@extends('adminlte::page')

@section('title', 'Empresas')

@section('content_header')
    <h1>Gestión de Empresas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <button class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btn-toggle-filtros">
                    <i class="fas fa-filter me-1"></i>
                    <span class="texto-btn d-none d-md-inline">Mostrar filtros</span>
                </button>
                <button class="btn btn-success" id="btn-nueva-empresa">
                    <i class="fas fa-plus"></i> Nueva Empresa
                </button>
            </div>

            <div class="table-responsive" style="max-height: 600px;">
                <table class="table table-bordered table-striped nowrap" id="tabla-empresas" style="width:100%;">
                    <thead class="table-primary">
                        <tr>
                            <th>RUC</th>
                            <th>Razón Social</th>
                            <th>Tipo_contribuyente</th>
                            <th>Régimen</th>
                            <th>Contabilidad</th>
                            <th>Contribuyente_especial</th>
                            <th>Agente_retencion</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th>Editar</th>
                        </tr>
                        <tr id="fila-filtros" class="filters">
                            <th><input type="text" class="form-control form-control-sm" placeholder="Ruc"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Razón Social"></th>
                            <th>
                                <select class="form-control form-control-sm">
                                    <option value="">Todos</option>
                                    <option value="Persona natural">Persona natural</option>
                                    <option value="Sociedad">Sociedad</option>
                                  </select>
                            </th>
                            <th><select class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="General">General</option>
                                <option value="Rimpe emprendedor">Rimpe emprendedor</option>
                                <option value="Rimpe negocio popular">Rimpe negocio popular</option>
                              </select></th>
                            <th>
                                <select class="form-control form-control-sm">
                                    <option value="">Todos</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                  </select>
                            </th>
                            <th>
                                <select class="form-control form-control-sm">
                                    <option value="">Todos</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                  </select>
                            </th>
                            <th>
                                <select class="form-control form-control-sm">
                                    <option value="">Todos</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                  </select>
                            </th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Email"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Teléfono"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="Dirección"></th>
                            <th>
                                <select class="form-control form-control-sm">
                                <option value="">Todas</option>
                                <option value="activo">Activa</option>
                                <option value="inactivo">Inactiva</option>
                              </select>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-empresa" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <form id="form-empresa">
                @csrf
                <input type="hidden" name="empresa_id" id="empresa_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEmpresaLabel">Nueva Empresa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="form-group col-md-3 mb-2">
                            <label for="ruc">RUC</label>
                            <input type="text" name="ruc" id="ruc" class="form-control" required>
                        </div>
                        <div class="form-group col-md-9 mb-2">
                            <label>Razón Social</label>
                            <input type="text" name="razon_social" id="razon_social" class="form-control" required>
                        </div>
                        <div class="form-group col-md-12 mb-2">
                            <label>Dirección matriz</label>
                            <input type="text" name="direccion" id="direccion" class="form-control">
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label>Cédula RL</label>
                            <input type="text" name="cedula_rep_leg" id="cedula_rep_leg" class="form-control">
                        </div>
                        <div class="form-group col-md-9 mb-2">
                            <label>Nombre representante legal</label>
                            <input type="text" name="nombre_rep_leg" id="nombre_rep_leg" class="form-control">
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label>Tipo contribuyente</label>
                            <select class="form-control" id="tipo_contribuyente" name="tipo_contribuyente" required>
                                <option value="01">Persona Natural</option>
                                <option value="02">Sociedad</option>
                              </select>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label>Régimen</label>
                            <select class="form-control" id="regimen" name="regimen" required>
                                <option value="1" Selected>General</option>
                                <option value="2">Rimpe emprendedor</option>
                                <option value="3">Rimpe negocio popular</option>
                              </select>
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            <label>Lleva contabilidad</label>
                            <select class="form-control" id="contabilidad" name="contabilidad" required>
                                <option value="NO" Selected>NO</option>
                                <option value="SI">SI</option>
                              </select>
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            <label>Contribuyente especial</label>
                            <select class="form-control" id="contribuyente_especial" name="contribuyente_especial" required>
                                <option value="NO" Selected>NO</option>
                                <option value="SI">SI</option>
                              </select>
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            <label>Agente Retención</label>
                            <select class="form-control" id="agente_retencion" name="agente_retencion" required>
                                <option value="NO" Selected>NO</option>
                                <option value="SI">SI</option>
                              </select>
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control">
                        </div>
                        <div class="form-group col-md-5 mb-2">
                            <label>Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            <label>Estado</label>
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="activo" Selected>Activa</option>
                                <option value="inactivo">Inactiva</option>
                              </select>
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
@stop

@section('js')
<script>
$(document).ready(function () {
$('#ruc').inputmask('9999999999999');
});

    $(function () {
        let tabla = $('#tabla-empresas').DataTable({
        dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
        processing: false,
        serverSide: true,
        fixedHeader: true,
        autoWidth: true,
        ajax: {
            url: '{{ route("empresas.data") }}',
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
                { data: 'ruc' },
                { data: 'razon_social' },
                { data: 'tipo_contribuyente', name: 'tipo_contribuyente' },
                { data: 'regimen' },
                { data: 'contabilidad' },
                { data: 'contribuyente_especial' },
                { data: 'agente_retencion' },
                { data: 'email' },
                { data: 'telefono' },
                { data: 'direccion' },
                { data: 'estado' },
                { data: 'acciones', orderable: false, searchable: false },
            ],
            language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        }
        });

        $('#fila-filtros input, #fila-filtros select').on('input change', function () {
            tabla.ajax.reload();
        });

        $('#btn-toggle-filtros').on('click', function () {
        const filtros = $('#fila-filtros');
        const visible = filtros.css('visibility') !== 'collapse';

        filtros.css('visibility', visible ? 'collapse' : 'visible');
        $(this).find('i').toggleClass('fa-filter fa-times');
        $(this).find('.texto-btn').text(visible ? 'Mostrar filtros' : 'Ocultar filtros');
    });

    $('#btn-nueva-empresa').on('click', function () {
        $('#form-empresa')[0].reset();
        $('#empresa_id').val('');
        $('#modalEmpresaLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nueva empresa');
        $('#modal-empresa').modal('show');
    });


         // Guardar o editar
         $('#form-empresa').on('submit', function (e) {
            e.preventDefault();
            const id = $('#empresa_id').val();
            const url = id ? `/empresas/update/${id}` : '{{ route("empresas.store") }}';
            const method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function (res) {
                    $('#modal-empresa').modal('hide');
                    tabla.ajax.reload();

                    Swal.fire({
                        icon: 'success',
                        title: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errores = xhr.responseJSON.errors;
                        let mensaje = '';
                        for (let campo in errores) {
                            mensaje += errores[campo][0] + '<br>';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Errores de validación',
                            html: mensaje
                        });
                    }
                }
            });
        });


        // Editar
        $(document).on('click', '.editar-empresa', function () {
        const id = $(this).data('id');
        $.get(`/empresas/${id}`, function (data) {
            $('#empresa_id').val(data.id);
            $('#ruc').val(data.ruc);
            $('#razon_social').val(data.razon_social);
            $('#cedula_rep_leg').val(data.cedula_rep_leg);
            $('#nombre_rep_leg').val(data.nombre_rep_leg);
            $('#tipo_contribuyente').val(data.tipo_contribuyente);
            $('#regimen').val(data.regimen);
            $('#contabilidad').val(data.contabilidad);
            $('#contribuyente_especial').val(data.contribuyente_especial);
            $('#agente_retencion').val(data.agente_retencion);
            $('#email').val(data.email);
            $('#telefono').val(data.telefono);
            $('#direccion').val(data.direccion);
            $('#estado').val(data.estado);
            $('#modalEmpresaLabel').html('<i class="fas fa-edit text-warning mr-2"></i> Editar Empresa');
            $('#modal-empresa').modal('show');
        });
    });

//para buscar los datos del ruc con la api
$('#ruc').on('change', function () {
    const ruc = $(this).val();

    if (ruc.length === 13) {
        Swal.fire({
            title: 'Consultando RUC...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: 'http://137.184.159.242:4000/api/sri-identification',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({ identification: ruc }),
            success: function (response) {
                Swal.close();

                const contribuyente = response.data?.datosContribuyente?.[0];
                const establecimientos = response.data?.establecimientos ?? [];

                if (contribuyente) {
                    $('#razon_social').val(contribuyente.razonSocial ?? '');
                    $('#tipo_contribuyente').val(contribuyente.tipoContribuyente === 'PERSONA NATURAL' ? '01' : '02');
                    $('#regimen').val(obtenerRegimen(contribuyente.regimen, contribuyente.categoria));
                    $('#contabilidad').val(contribuyente.obligadoLlevarContabilidad === 'SI' ? 'SI' : 'NO');
                    $('#contribuyente_especial').val(contribuyente.contribuyenteEspecial === 'SI' ? 'SI' : 'NO');
                    $('#agente_retencion').val(contribuyente.agenteRetencion === 'SI' ? 'SI' : 'NO');
                    $('#estado').val(contribuyente.estadoContribuyenteRuc === 'activo' ? 'activo' : 'inactivo');

                    // Representante legal
                    const representante = contribuyente.representantesLegales?.[0];
                    if (representante) {
                        $('#cedula_rep_leg').val(representante.identificacion ?? '');
                        $('#nombre_rep_leg').val(representante.nombre ?? '');
                    } else {
                        $('#cedula_rep_leg').val('');
                        $('#nombre_rep_leg').val('');
                    }
                }

                // Dirección: buscar la matriz
                const matriz = establecimientos.find(est => est.matriz === 'SI');
                $('#direccion').val(matriz?.direccionCompleta ?? '');
            },
            error: function (xhr) {
                Swal.close();
                Swal.fire('Error', 'No se pudo obtener información del RUC', 'error');
                console.log(xhr.responseText);
            }
        });
    }
});

});

function obtenerRegimen(regimenTexto, categoriaTexto) {
    if (!regimenTexto) return '1';

    const regimen = regimenTexto.toLowerCase();
    const categoria = (categoriaTexto ?? '').toLowerCase();

    if (regimen === 'rimpe' && categoria.includes('popular')) return '3';
    if (regimen === 'rimpe' && categoria.includes('emprendedor')) return '2';

    return '1'; // General
}

</script>
@stop
