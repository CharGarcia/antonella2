<div class="tab-pane fade" id="tab-comercial">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @include(
                    'components.select',
                    [
                        'nombre' => 'medida',
                        'label' => 'Unidad de medida',
                        'col' => 'col-md-4',
                        'opciones' => ['1' => 'Unidad'],
                        'mostrarPrimeraOpcion' => false,
                    ]
                )
                @include(
                    'components.select-with-button',
                    [
                        'nombre' => 'id_lista_precios',
                        'label' => 'Lista de Precios',
                        'opciones' => $listasPrecios,
                        'col' => 'col-md-4',
                        'buttonIcon' => 'fas fa-plus',
                        'buttonClass' => 'btn btn-outline-primary',
                        'buttonTitle' => 'Crear nueva lista de precios',
                        'modalId' => 'modal-lista-precios',
                        'selectTarget' => '#modal-producto #id_lista_precios', // ← ahora apunta al select de productos
                        'origin' => 'productos',
                    ]
                )
            </div>
        </div>
    </div>
</div>
