<div class="tab-pane fade" id="tab-configuracion">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @include('components.checkbox', ['nombre' => 'permitir_venta_con_deuda', 'label' => 'Permitir venta con deuda'])
                @include('components.checkbox', ['nombre' => 'aplica_descuento', 'label' => 'Aplicar descuentos personalizados'])
                @include('components.textarea', ['nombre' => 'notas', 'label' => 'Notas internas'])
            </div>
        </div>
    </div>
</div>
