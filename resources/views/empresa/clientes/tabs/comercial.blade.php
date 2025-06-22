<div class="tab-pane fade" id="tab-comercial">
    <div class="card">
        <div class="card-body">
    <div class="row">
        @include('components.input', ['nombre' => 'codigo_interno', 'label' => 'Código Interno'])
        @include('components.input', ['nombre' => 'categoria_cliente', 'label' => 'Categoría'])
        @include('components.input', ['nombre' => 'segmento', 'label' => 'Segmento'])
        @include('components.input', ['nombre' => 'vendedor_asignado', 'label' => 'Vendedor Asignado'])
        @include('components.input', ['nombre' => 'lista_precios', 'label' => 'Lista de Precios'])
        @include('components.input', ['nombre' => 'canal_venta', 'label' => 'Canal de Venta'])
        @include('components.input', ['nombre' => 'pais', 'label' => 'País'])
        @include('components.input', ['nombre' => 'provincia', 'label' => 'Provincia'])
        @include('components.input', ['nombre' => 'ciudad', 'label' => 'Ciudad'])
        @include('components.input', ['nombre' => 'zona', 'label' => 'Zona/Territorio'])
        @include('components.input', ['nombre' => 'clasificacion', 'label' => 'Clasificación'])
        @include('components.date', ['nombre' => 'inicio_relacion', 'label' => 'Inicio de relación'])
    </div>
</div>
</div>
</div>
