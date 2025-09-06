<div class="tab-pane fade" id="tab-contable">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @include('components.select', [
                    'nombre' => 'cta_contable_cliente',
                    'label' => 'Cta. Contable Cliente',
                    'opciones' => $cuentasContables ?? [],
                    'col' => 'col-md-4'
                ])

                @include('components.select', [
                    'nombre' => 'cta_anticipos_cliente',
                    'label' => 'Cta. Anticipos Cliente',
                    'opciones' => $cuentasContables ?? [],
                    'col' => 'col-md-4'
                ])

                @include('components.select', [
                    'nombre' => 'cta_ingresos_diferidos',
                    'label' => 'Cta. Ingresos Diferidos',
                    'opciones' => $cuentasContables ?? [],
                    'col' => 'col-md-4'
                ])

                @include('components.select', [
                    'nombre' => 'centro_costo',
                    'label' => 'Centro de Costo',
                    'opciones' => $centrosCosto ?? [],
                    'col' => 'col-md-4'
                ])

                @include('components.select', [
                    'nombre' => 'proyecto',
                    'label' => 'Proyecto',
                    'opciones' => $proyectos ?? [],
                    'col' => 'col-md-4'
                ])

                @include('components.select', [
                    'nombre' => 'segmento_contable',
                    'label' => 'Segmento Contable',
                    'opciones' => $segmentosContables ?? [],
                    'col' => 'col-md-4'
                ])

            </div>
        </div>
    </div>
</div>

