<div class="tab-pane fade" id="tab-financieros">
    <div class="card">
        <div class="card-body">
    <div class="row">
        @include('components.input', ['nombre' => 'cupo_credito', 'label' => 'Cupo de Crédito'])
        @include('components.input', ['nombre' => 'dias_credito', 'label' => 'Días de Crédito'])
        @include('components.input', ['nombre' => 'forma_pago', 'label' => 'Forma habitual de pago'])
        @include('components.textarea', ['nombre' => 'observaciones_crediticias', 'label' => 'Observaciones crediticias'])
    </div>
</div>
</div>
</div>
