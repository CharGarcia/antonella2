<!-- Modal Crear Usuario -->


<!-- Modal Crear Usuario -->
<div class="modal fade" id="modalCrearUsuario" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalCrearUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formCrearUsuarioAjax" method="POST" action="{{ route('usuarios.store') }}">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearUsuarioLabel">
                    <i class="fas fa-user-plus"></i> Crear nuevo usuario
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
                    <div class="invalid-feedback">El correo ya está registrado.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Enviar invitación</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </form>
  </div>
</div>
