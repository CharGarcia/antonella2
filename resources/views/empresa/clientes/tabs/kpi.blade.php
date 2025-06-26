
<div class="tab-pane fade" id="tab-kpi">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @include('components.input', [
                    'nombre' => 'total_ventas',
                    'label' => 'Total de ventas',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '0.01',
                    'readonly' => true
                ])

                @include('components.date', [
                    'nombre' => 'ultima_compra_fecha',
                    'label' => 'Fecha última compra',
                    'col' => 'col-md-4',
                    'disabled' => true
                ])

                @include('components.input', [
                    'nombre' => 'ultima_compra_monto',
                    'label' => 'Monto última compra',
                    'col' => 'col-md-4',
                        'type' => 'number',
                        'step' => '0.01',
                        'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'dias_promedio_pago',
                    'label' => 'Días promedio pago',
                    'col' => 'col-md-4',
                        'type' => 'number',
                        'step' => '1',
                        'readonly' => true
                 ])

                @include('components.input', [
                    'nombre' => 'saldo_por_cobrar',
                    'label' => 'Saldo por cobrar',
                    'col' => 'col-md-4',
                        'type' => 'number',
                        'step' => '0.01',
                        'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'promedio_mensual',
                    'label' => 'Promedio mensual compra',
                    'col' => 'col-md-4',
                        'type' => 'number',
                        'step' => '0.01',
                        'readonly' => true
                ])
            </div>
        </div>
    </div>
    </div>
