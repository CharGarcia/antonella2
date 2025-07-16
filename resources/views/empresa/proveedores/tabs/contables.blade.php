<div class="tab-pane fade" id="tab-contable">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @include('components.select', [
                    'nombre' => 'cuenta_por_pagar',
                    'label' => 'Cuenta por pagar',
                    'opciones' => $cuentasContables ?? [],
                    'col' => 'col-md-4'
                ])

                @include('components.select', [
                    'nombre' => 'cuenta_gasto_predeterminada',
                    'label' => 'Cuenta de gasto',
                    'opciones' => $cuentasContables ?? [],
                    'col' => 'col-md-4'
                ])

                @include('components.select', [
                    'nombre' => 'cuenta_inventario_predeterminada',
                    'label' => 'Cuenta de inventario',
                    'opciones' => $cuentasContables ?? [],
                    'col' => 'col-md-4'
                ])

                @include('components.select', [
                    'nombre' => 'cuenta_anticipo',
                    'label' => 'Cuenta de anticipo',
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
            </div>
        </div>
    </div>
</div>

