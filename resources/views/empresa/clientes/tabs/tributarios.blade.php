<div class="tab-pane fade" id="tab-tributarios">
    <div class="card">
        <div class="card-body">
    <div class="row">
        @include('components.checkbox', ['nombre' => 'agente_retencion', 'label' => 'Agente de retención'])
        @include('components.checkbox', ['nombre' => 'contribuyente_especial', 'label' => 'Contribuyente especial'])
        @include('components.checkbox', ['nombre' => 'obligado_contabilidad', 'label' => 'Obligado a llevar contabilidad'])
        @include('components.input', ['nombre' => 'regimen_tributario', 'label' => 'Régimen tributario'])
        @include('components.input', ['nombre' => 'retencion_fuente', 'label' => 'Retención en la fuente'])
        @include('components.input', ['nombre' => 'retencion_iva', 'label' => 'Retención de IVA'])
    </div>
</div>
</div>
</div>
