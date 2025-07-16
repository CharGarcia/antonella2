<div class="tab-pane fade" id="tab-documentos">
    <div class="card">
        <div class="card-body">
            <div class="row" id="documentos-container">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Documentos del Proveedor</label>
                    <div class="input-group mb-2">
                        <input type="file" name="documentos[]" class="form-control" multiple onchange="generarCamposTipo(this)">
                    </div>
                    <div id="tipos-container"></div>
                </div>

                <div class="col-md-12">
                    <div id="documentos-guardados">
                        {{-- Aquí se inyectarán los documentos existentes --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    //esto es para agregar el nombre a los documentos que se estan cargando
    function generarCamposTipo(input) {
             const container = document.getElementById('tipos-container');
        container.innerHTML = '';

        for (let i = 0; i < input.files.length; i++) {
            const div = document.createElement('div');
            div.className = 'mb-2 documento-nuevo';

            div.innerHTML = `
                <div class="input-group">
                    <input type="text" name="tipos[]" class="form-control" placeholder="Descripción del documento">
                    <div class="input-group-append">
                        <span class="input-group-text">${input.files[i].name}</span>
                        <button class="btn btn-danger btn-sm eliminar-documento" type="button">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(div);
        }

        // Activar botón de eliminar
        container.querySelectorAll('.eliminar-documento').forEach(button => {
            button.addEventListener('click', function () {
                this.closest('.documento-nuevo').remove();
            });
        });
    }
</script>

