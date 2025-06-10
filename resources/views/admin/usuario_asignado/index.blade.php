@extends('adminlte::page')

@section('title', 'Usuarios Asignados')

@section('content_header')
  <h1>Usuarios asignados</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <button class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btn-toggle-filtros">
            <i class="fas fa-times me-1"></i> <!-- Ícono de cerrar -->
            <span class="texto-btn d-none d-md-inline"> Ocultar filtros</span>
        </button>
        <button class="btn btn-success" id="btn-nuevo-asignar-usuario">
            <i class="fas fa-user-plus me-2"></i> Asignar Usuario
        </button>
    </div>
 <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
    <table class="table table-bordered table-striped nowrap" id="tabla-usuarios-asignados" style="width:100%;">
      <thead class="table-primary">
        <tr>
          <th>Cédula</th>
          <th>Usuario</th>
          <th>Email</th>
          <th>Administrado por</th>
          <th>Acción</th>
        </tr>
            <tr id="fila-filtros" class="filters" style="visibility: visible;">
            <th><input type="text" class="form-control form-control-sm" placeholder="Cédula de usuario" /></th>
            <th><input type="text" class="form-control form-control-sm" placeholder="Nombre de usuario" /></th>
            <th><input type="text" class="form-control form-control-sm" placeholder="Email de usuario" /></th>
            <th><input type="text" class="form-control form-control-sm" placeholder="Administrado por" /></th>
            <th></th>
            </tr>
      </thead>
    </table>
  </div>
</div>
</div>

{{-- Modal de asignación --}}
<div class="modal fade" id="modal-usuario-asignado" data-backdrop="static" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="form-usuario-asignado">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"id="modalUsuarioAsignadoLabel" >Asignar un usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                  <span aria-hidden="true">&times;</span>
                </button>
        </div>
        <div class="modal-body row">
          <div class="form-group col-md-12">
            <label for="user_id">Usuario</label>
            <select name="user_id" id="user_id" class="form-control" required>
              <option value="">-- Selecciona --</option>
              @foreach(\App\Models\User::where('status', true)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'user');
                })
                ->orderBy('name')
                ->get() as $user)
                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
            @endforeach
            </select>
          </div>
          <div class="form-group col-md-12">
            <label for="admin_id">Asignar a</label>
            <select name="admin_id" id="admin_id" class="form-control" required>
              <option value="">-- Selecciona --</option>
               @foreach(\App\Models\User::where('status', true)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'admin');
                })
                ->orderBy('name')
                ->get() as $user)
                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
            @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>
@stop

@push('js')
<script>
$(function(){
  // Inicializar DataTable
  let tabla = $('#tabla-usuarios-asignados').DataTable({
    dom: '<"row"<"col-md-2 text-left">>rt<"row"<"col-md-12 text-center"p>>',
    processing: false,
    serverSide: true,
    autoWidth: true,
    ajax: {
            url: '{{ route("usuario_asignado.data") }}',
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
            { data: 'cedula' },
            { data: 'usuario' },
            { data: 'email' },
            { data: 'admin' }, // esta es nueva
            { data: 'accion', orderable: false, searchable: false }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        }
  });

  $('#tabla-usuarios-asignados thead').on('input change', '.filters input, .filters select', function () {
        tabla.ajax.reload();
    });

     $('#btn-toggle-filtros').on('click', function () {
        const filtros = $('#fila-filtros');
        const visible = filtros.css('visibility') !== 'collapse';

        filtros.css('visibility', visible ? 'collapse' : 'visible');
        $(this).find('i').toggleClass('fa-filter fa-times');
        $(this).find('.texto-btn').text(visible ? 'Mostrar filtros' : 'Ocultar filtros');
    });

  // Mostrar modal
  $('#btn-nuevo-asignar-usuario').on('click', function () {
        $('#form-usuario-asignado')[0].reset();
        $('#modalUsuarioAsignadoLabel').html('<i class="fas fa-clipboard-check text-success mr-2"></i> Administrar un usuario');
        $('#modal-usuario-asignado').modal('show');
    });

        // Inicializar Select2
 $('#user_id').select2({
    theme: 'bootstrap4',
    dropdownParent: $('#modalUsuarioAsignadoLabel'),
    width: '100%',
    placeholder: 'Seleccione un usuario'
});

$('#admin_id').select2({
    theme: 'bootstrap4',
    dropdownParent: $('#modalUsuarioAsignadoLabel'),
    width: '100%',
    placeholder: 'Seleccione un administrador'
});

      // Guardar asignación
$('#form-usuario-asignado').submit(function (e) {
        e.preventDefault();
        $.post('{{ route("usuario_asignado.store") }}', $(this).serialize(), function (response) {
            if (response.success) {
                $('#modal-usuario-asignado').modal('hide');
                $('#form-usuario-asignado')[0].reset();
                $('#user_id, #admin_id').val(null).trigger('change'); // para resetear Select2
                tabla.ajax.reload();
                Swal.fire('Guardado', response.message, 'success');
            }
        }).fail(function (xhr) {
            let mensaje = xhr.responseJSON?.message || 'Error al guardar.';
            Swal.fire('Atención', mensaje, 'warning');
        });
    });


// Eliminar asignación
$('#tabla-usuarios-asignados').on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará la asignación.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url("usuario-asignado/eliminar") }}/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    $('#tabla-usuarios-asignados').DataTable().ajax.reload(null, false);
                    Swal.fire('Eliminado', response.message, 'success');
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar la asignación.', 'error');
                }
            });
        }
    });
});


});
</script>
@endpush
