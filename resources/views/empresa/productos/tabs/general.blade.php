<div class="tab-pane fade show active" id="tab-general">
    <div class="card">
        <div class="card-body">
            <div class="row">
            @include('components.input', ['nombre' => 'codigo', 'label' => 'Código', 'col' => 'col-md-4'])
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

            @include('components.input', ['nombre' => 'descripcion', 'label' => 'Descripción', 'col' => 'col-md-12'])
            @include('components.input', ['nombre' => 'precio_base', 'label' => 'Precio base',
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0',
                        'col' => 'col-md-4'])
               @include('components.select', [
                    'nombre' => 'tarifa_iva_id',
                    'label' => 'Tarifa IVA',
                    'col' => 'col-md-4',
                    'value' => '4',
                    'opciones' => $tarifaIva,
                    'mostrarPrimeraOpcion' => false
                ])
                @include('components.input', ['nombre' => 'pvp', 'label' => 'Precio final',
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0',
                            'col' => 'col-md-4',
                            'claseExtra' => 'text-right'])
            </div>
        </div>
    </div>
</div>
