<div class="tab-pane fade" id="tab-financieros">
    <div class="card">
        <div class="card-body">
    <div class="row">
        @include('components.input', [
            'nombre' => 'cupo_credito',
            'label' => 'Cupo de Crédito',
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'col' => 'col-md-3'
        ])

        @include('components.input', [
            'nombre' => 'dias_credito',
            'label' => 'Días de Crédito',
            'type' => 'number',
            'step' => '1',
            'min' => '0',
            'value' => 30,
            'required' => false,
            'col' => 'col-md-3'
        ])

        @include('components.select', [
            'nombre' => 'forma_pago',
            'label' => 'Forma habitual de cobro',
            'opciones' => $formasPago,
            'value' => $cliente->forma_pago ?? null,
            'required' => false,
            'col' => 'col-md-6'
        ])

        {{-- @include('components.select', [
            'nombre' => 'forma_pago',
            'label' => 'Forma habitual de cobro',
            'opciones' => $formasPago, 'mostrarPrimeraOpcion' => false, 'col' => 'col-md-4'
        ]) --}}

        @include('components.textarea', ['nombre' => 'observaciones_crediticias', 'label' => 'Observaciones crediticias'])
    </div>
</div>
</div>
</div>
