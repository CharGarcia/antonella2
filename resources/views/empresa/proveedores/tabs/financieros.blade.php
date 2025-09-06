<div class="tab-pane fade" id="tab-financieros">
    <div class="card">
        <div class="card-body">
            <div class="row">

                @include('components.select', [
                    'nombre' => 'id_banco',
                    'label' => 'Banco',
                    'opciones' => $bancos,
                    'value' => $proveedor->id_banco ?? null,
                    'col' => 'col-md-4',
                    'required' => false,
                    'mostrarPrimeraOpcion' => true
                ])

                @include('components.select', [
                    'nombre' => 'tipo_cuenta',
                    'label' => 'Tipo de Cuenta',
                    'opciones' => [
                        'AHORROS' => 'Ahorros',
                        'CORRIENTE' => 'Corriente',
                        'VIRTUAL' => 'Virtual'
                    ],
                    'col' => 'col-md-4',
                    'required' => false
                ])

                @include('components.input', [
                    'nombre' => 'numero_cuenta',
                    'label' => 'Número de Cuenta',
                    'type' => 'number',
                    'step' => '0.01',
                    'min' => '0',
                    'col' => 'col-md-4',
                    'required' => false
                ])


                @include('components.input', [
                    'nombre' => 'limite_credito',
                    'label' => 'Límite de Crédito',
                    'type' => 'number',
                    'step' => '0.01',
                    'min' => '0',
                    'col' => 'col-md-3'
                ])

                @include('components.input', [
                    'nombre' => 'dias_credito',
                    'label' => 'Días de Crédito',
                    'type' => 'number',
                    'min' => '0',
                    'col' => 'col-md-3'
                ])

             @include('components.select', [
                    'nombre' => 'forma_pago',
                    'label' => 'Forma de Pago',
                    'opciones' => $formasPago,
                    'value' => $cliente->forma_pago ?? null,
                    'required' => false,
                    'col' => 'col-md-6',
                    'mostrarPrimeraOpcion' => true
                ])

                @include('components.select', [
                    'nombre' => 'nivel_riesgo',
                    'label' => 'Nivel de Riesgo',
                    'opciones' => [
                        'ALTO' => 'Alto',
                        'MEDIO' => 'Medio',
                        'BAJO' => 'Bajo',
                    ],
                    'col' => 'col-md-4',
                    'mostrarPrimeraOpcion' => true
                ])

                @include('components.textarea', [
                    'nombre' => 'observaciones_crediticias',
                    'label' => 'Observaciones Crediticias',
                    'col' => 'col-md-12',
                    'rows' => 4
                ])

            </div>
        </div>
    </div>
</div>

