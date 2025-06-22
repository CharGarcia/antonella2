<div class="tab-pane fade" id="tab-kpi">
    <div class="row">
        @include('components.input', ['nombre' => 'total_ventas', 'label' => 'Total de ventas'])
        @include('components.date', ['nombre' => 'ultima_compra_fecha', 'label' => 'Fecha última compra'])
        @include('components.input', ['nombre' => 'ultima_compra_monto', 'label' => 'Monto última compra'])
        @include('components.input', ['nombre' => 'dias_promedio_pago', 'label' => 'Días promedio pago'])
        @include('components.input', ['nombre' => 'saldo_por_cobrar', 'label' => 'Saldo por cobrar'])
        @include('components.input', ['nombre' => 'promedio_mensual', 'label' => 'Promedio mensual compra'])
    </div>
</div>
