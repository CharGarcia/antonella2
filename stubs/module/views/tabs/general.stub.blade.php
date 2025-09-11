<div class="tab-pane fade show active" id="tab-general">
    <div class="card">
        <div class="card-body">
            <div class="row">
               @include('components.input', ['nombre' => 'codigo', 'label' => 'Código', 'col' => 'col-md-8'])
            @include('components.input', ['nombre' => 'descripcion', 'label' => 'Descripción', 'col' => 'col-md-12'])
            @include('components.select', [
                    'nombre' => 'tipo_id',
                    'label' => 'Tipo',
                    'opciones' => ['1' => 'Producto', '2' => 'Servicio', '3' => 'Activo fijo', '4' => 'Kit/combo', '5' => 'Bien no inventariable'],
                    'col' => 'col-md-4',
                    'mostrarPrimeraOpcion' => false
                ])
                @include('components.select', [
                    'nombre' => 'estado',
                    'label' => 'Estado',
                    'opciones' => ['activo' => 'Activo', 'inactivo' => 'Inactivo'],
                    'col' => 'col-md-4',
                    'mostrarPrimeraOpcion' => false
                ])
            </div>
        </div>
    </div>
</div>
