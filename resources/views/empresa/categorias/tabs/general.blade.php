<div class="tab-pane fade show active" id="tab-general">
    <div class="card">
        <div class="card-body">
            <div class="row">
               @include('components.input', ['nombre' => 'nombre', 'label' => 'Nombre', 'col' => 'col-md-8'])
               @include('components.select', [
                    'nombre' => 'status',
                    'label' => 'Estado',
                    'opciones' => ['activo' => 'Activo', 'inactivo' => 'Inactivo'],
                    'col' => 'col-md-4',
                    'mostrarPrimeraOpcion' => false
                ])
               @include('components.input', ['nombre' => 'descripcion', 'label' => 'Descripción', 'col' => 'col-md-12'])
            </div>
        </div>
    </div>
</div>
