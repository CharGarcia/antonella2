<div class="tab-pane fade" id="tab-financieros">
    <div class="card">
        <div class="card-body">
    <div class="row">
        @include('components.input', [
            'nombre' => 'monto_ventas_asignado',
            'label' => 'Monto de ventas asignado',
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'col' => 'col-md-3'
        ])
        @include('components.textarea', ['nombre' => 'informacion_adicional', 'label' => 'Información adicional','col' => 'col-md-9'])
    </div>
</div>
</div>
</div>
