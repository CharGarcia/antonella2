<div class="tab-pane fade" id="tab-comercial">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @include('components.input', ['nombre' => 'nombre_comercial', 'label' => 'Nombre comercial', 'col' => 'col-md-12'])
                @include('components.input', ['nombre' => 'codigo_interno', 'label' => 'Código Interno', 'col' => 'col-md-4'])
                @include(
                    'components.select',
                    [
                        'nombre' => 'categoria_cliente',
                        'label' => 'Categoría',
                        'opciones' => ['Minorista', 'Mayorista', 'Distribuidor', 'Cliente Final'],
                        'col' => 'col-md-4',
                        'mostrarPrimeraOpcion' => true,
                    ]
                )
                @include(
                    'components.select',
                    [
                        'nombre' => 'segmento',
                        'label' => 'Segmento',
                        'opciones' => ['Retail', 'Industrial', 'Corporativo', 'Educación', 'Gobierno'],
                        'col' => 'col-md-4',
                        'mostrarPrimeraOpcion' => true,
                    ]
                )
                @include(
                    'components.select',
                    [
                        'nombre' => 'vendedor_asignado',
                        'label' => 'Vendedor Asignado',
                        'opciones' => $vendedores,
                        'mostrarPrimeraOpcion' => true,
                        'col' => 'col-md-4',
                    ]
                )

                {{-- Lista de Precios + botón para crear (abre TU modal existente) --}}
                @include(
                    'components.select-with-button',
                    [
                        'nombre' => 'id_lista_precios',
                        'label' => 'Lista de Precios',
                        'opciones' => $listasPrecios, // array id => nombre
                        'col' => 'col-md-4',
                        'buttonIcon' => 'fas fa-plus',
                        'buttonClass' => 'btn btn-outline-primary',
                        'buttonTitle' => 'Crear nueva lista de precios',
                        'modalId' => 'modal-lista-precios', // ← id del modal de listas
                        'selectTarget' => '#modal-cliente #id_lista_precios', // ← A QUÉ SELECT ACTUALIZAR
                        'origin' => 'clientes', // (opcional)
                    ]
                )
                @include(
                    'components.select',
                    [
                        'nombre' => 'canal_venta',
                        'label' => 'Canal de Venta',
                        'opciones' => ['Directo', 'Distribuidor', 'Online', 'Televentas', 'Marketplace'],
                        'mostrarPrimeraOpcion' => true,
                        'col' => 'col-md-4',
                    ]
                )
                @include('components.input', ['nombre' => 'pais', 'label' => 'País', 'value' => 'ECUADOR', 'col' => 'col-md-4'])
                @include('components.input', ['nombre' => 'provincia', 'label' => 'Provincia', 'col' => 'col-md-4'])
                @include('components.input', ['nombre' => 'ciudad', 'label' => 'Ciudad', 'col' => 'col-md-4'])
                @include('components.input', ['nombre' => 'zona', 'label' => 'Zona/Territorio', 'col' => 'col-md-4'])
                @include(
                    'components.select',
                    [
                        'nombre' => 'clasificacion',
                        'label' => 'Clasificación',
                        'opciones' => ['A', 'B', 'C'],
                        'mostrarPrimeraOpcion' => true,
                        'col' => 'col-md-4',
                    ]
                )
                @include(
                    'components.date',
                    [
                        'nombre' => 'inicio_relacion',
                        'label' => 'Inicio de relación',
                        'value' => old(
                            'inicio_relacion',
                            isset($cliente) && $cliente->inicio_relacion_formatted
                                ? $cliente->inicio_relacion_formatted
                                : now()->format('d/m/Y'),
                        ),
                        'col' => 'col-md-4',
                    ]
                )
            </div>
        </div>
    </div>
</div>
