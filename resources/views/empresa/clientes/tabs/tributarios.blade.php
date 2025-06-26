<div class="tab-pane fade" id="tab-tributarios">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @include('components.checkbox', ['nombre' => 'agente_retencion', 'label' => 'Agente de retención'])
                @include('components.checkbox', ['nombre' => 'contribuyente_especial', 'label' => 'Contribuyente especial'])
                @include('components.checkbox', ['nombre' => 'obligado_contabilidad', 'label' => 'Obligado a llevar contabilidad'])

                @include('components.select', [
                    'nombre' => 'regimen_tributario',
                    'label' => 'Régimen tributario',
                    'opciones' => ['1' => 'General', '2' => 'Rimpe emprendedor', '3' => 'Rimpe negocio popular'],
                    'col' => 'col-md-6', 'mostrarPrimeraOpcion' => true
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
