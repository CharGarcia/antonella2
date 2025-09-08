
@extends('adminlte::page')
@section('title', 'Asignar Establecimiento')

@section('content_header')
    <h1>Asignación de establecimientos y módulos a usuarios</h1>
@stop
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <button class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btn-toggle-filtros">
                <i class="fas fa-filter me-1"></i>
                <span class="texto-btn d-none d-md-inline">Mostrar filtros</span>
            </button>
            <button class="btn btn-success" id="btn-nueva-asignacion">
                <i class="fas fa-plus"></i> Asignar establecimiento
            </button>
        </div>

        <div class="table-responsive" style="max-height: 600px;">
            <table class="table table-bordered table-striped" id="tabla-asignacion" style="width:100%;">
                <thead class="table-primary">
                    <tr>
                        <th>Cédula</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Establecimiento</th>
                        <th>Módulos</th>
                        <th>Eliminar</th>
                    </tr>
                    <tr id="fila-filtros" class="filters" style="visibility: visible;">
                        <th><input type="text" class="form-control form-control-sm" placeholder="Cédula"></th>
                        <th><input type="text" class="form-control form-control-sm" placeholder="Usuario"></th>
                        <th><input type="text" class="form-control form-control-sm" placeholder="Email"></th>
                        <th><input type="text" class="form-control form-control-sm" placeholder="Establecimiento"></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal agregar establecimiento-->
<div class="modal fade" id="modalAsignacion" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <form id="form-asignacion">
            @csrf
            <div class="modal-content">

                <div class="modal-header">
                        <h5 class="modal-title" id="modalAsignarEstablecimientoLabel">Asignar Establecimiento a Usuario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span>&times;</span>
                        </button>
                    </div>

                <div class="modal-body row">
                    <div class="form-group col-md-12 mb-2">
                        <label for="user_id">Usuario</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        @foreach(\App\Models\Admin\User::where('estado', 'activo')->orderBy('name')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
                        @endforeach
                    </select>
                    </div>
                    <div class="form-group col-md-12 mb-2">
                        <label for="establecimiento_id">Establecimiento</label>
                        <select name="establecimiento_id" id="establecimiento_id" class="form-control" required>
                            @foreach(\App\Models\Admin\Establecimiento::orderBy('nombre_comercial')->get() as $establecimiento)
                                <option value="{{ $establecimiento->id }}">{{ $establecimiento->nombre_comercial }} - {{ $establecimiento->serie }}</option>
                            @endforeach
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

<!-- Modal agregar modulos-->
<div class="modal fade" id="modalModulos" data-backdrop="static" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <form id="form-modulos">
      @csrf
      <input type="hidden" id="usuario_establecimiento_id" name="usuario_establecimiento_id">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="titulo-modal-permisos">Asignar Módulos a Usuario</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div id="tabla-permisos-container" class="table-responsive"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar Permisos</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

@stop

@section('js')
<script>
$(function () {
const tabla = $('#tabla-asignacion').DataTable({
    dom: '<"row"<"col-md-12">>rt<"row"<"col-md-12 text-center"p>>',
    serverSide: true,
    processing: false,
    fixedHeader: true,
    ajax: {
        url: '{{ route("asignacion_establecimiento_usuario.data") }}',
        data: function (d) {
            $('.filters th').each(function (i) {
                const input = $(this).find('input');
                if (input.length) {
                    d['columns[' + i + '][search][value]'] = input.val();
                }
            });
        }
    },
    columns: [
        { data: 'cedula', name: 'users.cedula' },
        { data: 'usuario', name: 'users.name' },
        { data: 'email',   name: 'users.email' },
        { data: 'establecimiento', name: 'establecimientos.nombre_comercial'},
        { data: 'modulos', orderable: false, searchable: false },
        { data: 'eliminar', orderable: false, searchable: false }
    ],
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
    }
});


    // Filtros dinámicos
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

    // Mostrar modal nuevo
    $('#btn-nueva-asignacion').on('click', function () {
        $('#form-asignacion')[0].reset();
        $('#user_id').val('').trigger('change');
        $('#establecimiento_id').val('').trigger('change');
        $('#modalAsignarEstablecimientoLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Nuevo establecimiento a usuario');
        $('#modalAsignacion').modal('show');
    });

    // Inicializar Select2
 $('#user_id').select2({
    theme: 'bootstrap4',
    dropdownParent: $('#modalAsignacion'),
    width: '100%',
    placeholder: 'Seleccione un usuario'
});

$('#establecimiento_id').select2({
    theme: 'bootstrap4',
    dropdownParent: $('#modalAsignacion'),
    width: '100%',
    placeholder: 'Seleccione un establecimiento'
});


    // Guardar asignación
    $('#form-asignacion').submit(function (e) {
        e.preventDefault();
        $.post('{{ route("asignacion_establecimiento_usuario.asignar") }}', $(this).serialize(), function (response) {
            if (response.success) {
                $('#modalAsignacion').modal('hide');
                tabla.ajax.reload();
                Swal.fire('Guardado', response.message, 'success');
            }
        }).fail(function (xhr) {
            let mensaje = xhr.responseJSON?.message || 'Error al guardar.';
            Swal.fire('Atención', mensaje, 'warning');
        });
    });

    // Eliminar asignación de la establecimiento
$(document).on('click', '.eliminar-asignacion', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: '¿Está seguro?',
        text: 'Esta acción eliminará la asignación.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/asignacion_establecimiento_usuario/eliminar/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    $('#tabla-asignacion').DataTable().ajax.reload(null, false);
                    Swal.fire('Eliminado', response.message, 'success');
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar la asignación.', 'error');
                }
            });
        }
    });
});

//para asignar permisos al usuario
$(document).on('click', '.modulos-asignacion', function () {
    const id = $(this).data('id');
    const usuario = $(this).data('user');
    const establecimiento = $(this).data('establecimiento');

    // Mostrar nombres en el título del modal
    $('#titulo-modal-permisos').html(`
        <i class="fas fa-cogs mr-1"></i>
        Permisos para <strong>${usuario}</strong> en <strong>${establecimiento}</strong>
    `);

    // Continuar con la carga de permisos
    $('#usuario_establecimiento_id').val(id);
    $('#tabla-permisos-container').html('<div class="text-center text-muted">Cargando módulos...</div>');
    $('#modalModulos').modal('show');

    $.get('/asignacion_establecimiento_usuario/permisos/' + id, function (html) {
        $('#tabla-permisos-container').html(html);
    }).fail(function () {
        $('#tabla-permisos-container').html('<div class="text-danger">Error al cargar los módulos.</div>');
    });
});


// Aplicar perfil guardado
/*
$(document).on('click', '#btn-aplicar-perfil', function () {
    const perfil = new URLSearchParams(localStorage.getItem('ultimo_perfil_permisos'));
    for (const [key, value] of perfil.entries()) {
        const input = $(`[name="${key}"]`);
        if (input.length && input.attr('type') === 'checkbox') {
            input.prop('checked', true);
        }
    }
    $(this).closest('.alert').remove();
});
*/


//Guardar permisos desde el formulario
$('#form-modulos').submit(function (e) {
    e.preventDefault();
    const datos = $(this).serialize();
    // Guardar los permisos en localStorage
    localStorage.setItem('ultimo_perfil_permisos', datos);

    $.post('/asignacion_establecimiento_usuario/permisos/guardar', datos, function () {
        Swal.fire('Guardado', 'Permisos actualizados correctamente.', 'success');
        $('#modalModulos').modal('hide');
    }).fail(function () {
        Swal.fire('Error', 'No se pudieron guardar los permisos.', 'error');
    });
});

//para marcar todos los items de crear, ver, modificar o eliminar
$(document).on('change', '.check-todos', function () {
    const accion = $(this).data('accion');
    const checkEstado = $(this).is(':checked');

    $(`input[name^="permisos"][name$="[${accion}]"]`).prop('checked', checkEstado);
});

// Marcar todos los checkboxes
$(document).on('click', '#btn-marcar-todos', function () {
    $('#tabla-permisos-container input[type="checkbox"]').prop('checked', true);
});

// Desmarcar todos los checkboxes
$(document).on('click', '#btn-desmarcar-todos', function () {
    $('#tabla-permisos-container input[type="checkbox"]').prop('checked', false);
});

//marcar y desmarcar por fila
$(document).on('change', '.check-fila', function () {
    const submenuId = $(this).data('submenu');
    const estado = $(this).is(':checked');

    $(`input[name^="permisos[${submenuId}]"]`).prop('checked', estado);
});

//para buscar los modulos
$(document).on('input', '#buscador-permisos', function () {
    const filtro = $(this).val().toLowerCase();

    $('#acordeon-permisos .card').each(function () {
        const card = $(this);
        let coincidencias = 0;

        // Recorrer cada fila del submenú
        card.find('tbody tr').each(function () {
            const fila = $(this);
            const texto = fila.text().toLowerCase();

            if (texto.includes(filtro)) {
                fila.show();
                coincidencias++;
            } else {
                fila.hide();
            }
        });

        // Mostrar el menú (card) siempre
        card.show();

        // Expandir si hay coincidencias, contraer si no
        if (coincidencias > 0) {
            card.find('.collapse').addClass('show');
        } else {
            card.find('.collapse').removeClass('show');
        }
    });
});


});
</script>
@stop
