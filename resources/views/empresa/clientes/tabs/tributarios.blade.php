<div class="tab-pane fade" id="tab-tributarios">
    <div class="card">
        <div class="card-body">

            <!-- Fila 1: Checkboxes -->
            <div class="d-flex flex-wrap align-items-center">
                @include('components.checkbox', [
                    'nombre' => 'agente_retencion',
                    'label' => 'Agente de retención',
                    'col' => 'me-3'
                ])
                @include('components.checkbox', [
                    'nombre' => 'obligado_contabilidad',
                    'label' => 'Lleva contabilidad',
                    'col' => 'me-3'
                ])
                @include('components.checkbox', [
                    'nombre' => 'parte_relacionada',
                    'label' => 'Parte relacionada',
                    'col' => 'me-3'
                ])
                @include('components.checkbox', [
                    'nombre' => 'contribuyente_especial',
                    'label' => 'Contribuyente especial',
                    'col' => 'me-3'
                ])
            </div>

            <!-- Fila 2: Selects e Inputs -->
            <div class="row mt-3">
                @include('components.select', [
                    'nombre' => 'regimen_tributario',
                    'label' => 'Régimen tributario',
                    'opciones' => [
                        '1' => 'General',
                        '2' => 'Rimpe emprendedor',
                        '3' => 'Rimpe negocio popular'
                    ],
                    'col' => 'col-md-6',
                    'mostrarPrimeraOpcion' => true
                ])
                @include('components.input', [
                    'nombre' => 'retencion_fuente',
                    'label' => '% Retención en la fuente',
                    'type' => 'number',
                    'step' => '1',
                    'min' => '0',
                    'col' => 'col-md-3'
                ])
                @include('components.input', [
                    'nombre' => 'retencion_iva',
                    'label' => '% Retención de IVA',
                    'type' => 'number',
                    'step' => '1',
                    'min' => '0',
                    'col' => 'col-md-3'
                ])
            </div>

        </div>
    </div>
</div>
