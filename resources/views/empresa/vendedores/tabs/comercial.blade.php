
<div class="tab-pane fade" id="tab-comercial">
    <div class="card">
        <div class="card-body">
    <div class="row">
        @include('components.input', ['nombre' => 'codigo_interno', 'label' => 'Código Interno', 'col' => 'col-md-4'])
        @include('components.select', [
            'nombre' => 'perfil',
            'label' => 'Perfil',
            'opciones' => ['Asesor', 'Vendedor', 'Supervisor'],
            'col' => 'col-md-4', 'mostrarPrimeraOpcion' => true
        ])
        @include('components.input', ['nombre' => 'pais', 'label' => 'País', 'value' => 'ECUADOR', 'col' => 'col-md-4'])
        @include('components.input', ['nombre' => 'provincia', 'label' => 'Provincia', 'col' => 'col-md-4'])
        @include('components.input', ['nombre' => 'ciudad', 'label' => 'Ciudad', 'col' => 'col-md-4'])
        @include('components.input', ['nombre' => 'zona', 'label' => 'Zona/Territorio', 'col' => 'col-md-4'])
        @include('components.date', [
            'nombre' => 'inicio_relacion',
            'label' => 'Inicio de relación',
            'value' => now()->toDateString(),
            'disabled' => true, 'col' => 'col-md-4'
        ])
    </div>
</div>
</div>
</div>
