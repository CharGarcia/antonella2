
<div class="tab-pane fade" id="tab-kpi">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @include('components.input', [
                    'nombre' => 'total_compras_anual',
                    'label' => 'Total Compras Anual',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '0.01',
                    'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'cantidad_facturas',
                    'label' => 'Cantidad de Facturas',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '1',
                    'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'monto_promedio_compra',
                    'label' => 'Monto Promedio de Compra',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '0.01',
                    'readonly' => true
                ])

                @include('components.date', [
                    'nombre' => 'ultima_compra_fecha',
                    'label' => 'Fecha Última Compra',
                    'col' => 'col-md-4',
                    'disabled' => true
                ])

                @include('components.input', [
                    'nombre' => 'ultima_compra_monto',
                    'label' => 'Monto Última Compra',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '0.01',
                    'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'dias_promedio_pago',
                    'label' => 'Días Promedio de Pago',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '1',
                    'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'porcentaje_entregas_a_tiempo',
                    'label' => 'Entregas a Tiempo (%)',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '0.01',
                    'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'porcentaje_entregas_fuera_plazo',
                    'label' => 'Entregas Fuera de Plazo (%)',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '0.01',
                    'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'porcentaje_devoluciones',
                    'label' => 'Porcentaje de Devoluciones (%)',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '0.01',
                    'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'porcentaje_reclamos',
                    'label' => 'Porcentaje de Reclamos (%)',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '0.01',
                    'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'cantidad_incidentes',
                    'label' => 'Cantidad de Incidentes',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '1',
                    'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'saldo_por_pagar',
                    'label' => 'Saldo por Pagar',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '0.01',
                    'readonly' => true
                ])

                @include('components.input', [
                    'nombre' => 'promedio_mensual',
                    'label' => 'Promedio Mensual',
                    'col' => 'col-md-4',
                    'type' => 'number',
                    'step' => '0.01',
                    'readonly' => true
                ])

            </div>
        </div>
    </div>
</div>

