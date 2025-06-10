@extends('adminlte::page')
@section('title', 'Completar Registro')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="col-md-6">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h4>Completa tu Registro</h4>
            </div>
            <div class="card-body">
                <form id="formRegistroUsuarioAjax" method="POST" action="{{ route('completar-registro.guardar', $user->remember_token) }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name">Nombre completo</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="cedula">Cédula</label>
                        <input id="cedula" type="text" name="cedula" pattern="\d{10}" maxlength="10"
                        class="form-control @error('cedula') is-invalid @enderror" required>
                        @error('cedula')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">Contraseña</label>
                        <input id="password" type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="password_confirmation">Confirmar Contraseña</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            class="form-control" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            Guardar y Activar Cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
$(document).ready(function () {
    $('#formRegistroUsuarioAjax').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');
        let token = $('input[name="_token"]').val();

        let nombreInput = form.find('input[name="name"]');
        let cedulaInput = form.find('input[name="cedula"]');
        let passwordInput = form.find('input[name="password"]');
        let confirmInput = form.find('input[name="password_confirmation"]');
        let boton = form.find('button[type="submit"]');

        // Limpiar errores previos
        form.find('input').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        boton.prop('disabled', true);

        Swal.fire({
            title: 'Procesando...',
            text: 'Registrando usuario...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: token,
                name: nombreInput.val(),
                cedula: cedulaInput.val(),
                password: passwordInput.val(),
                password_confirmation: confirmInput.val()
            },
            success: function (response) {
                Swal.close();

                Swal.fire({
                    icon: 'success',
                    title: 'Registro completado',
                    text: response.message || 'Tu cuenta ha sido activada.',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    window.location.href = response.redirect || '/login';
                });
            },
            error: function (xhr) {
                Swal.close();
                boton.prop('disabled', false);

                if (xhr.status === 422) {
                    let errores = xhr.responseJSON.errors;
                    for (let campo in errores) {
                        let input = form.find(`[name="${campo}"]`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback">${errores[campo][0]}</div>`);
                    }
                } else {
                    Swal.fire('Error', 'Ocurrió un error inesperado.', 'error');
                }
            }
        });
    });
});
</script>
