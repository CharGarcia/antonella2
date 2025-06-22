<div class="tab-pane fade show active" id="tab-general">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @include('components.select', ['nombre' => 'tipo_identificacion', 'label' => 'Tipo Identificación', 'opciones' => ['04' => 'RUC', '05' => 'Cédula', '06' => 'Pasaporte', '07' => 'Consumidor final', '08' => 'Exterior'], 'col' => 'col-md-4'])
                @include('components.input', ['nombre' => 'numero_identificacion', 'label' => 'Número Identificación', 'col' => 'col-md-4'])
                @include('components.select', ['nombre' => 'estado', 'label' => 'Estado', 'opciones' => ['activo' => 'Activo', 'inactivo' => 'Inactivo'], 'col' => 'col-md-4'])
                @include('components.input', ['nombre' => 'nombre', 'label' => 'Razón Social / Nombre', 'col' => 'col-md-12'])
                @include('components.input', ['nombre' => 'direccion', 'label' => 'Dirección', 'col' => 'col-md-12'])
                @include('components.input', ['nombre' => 'email', 'label' => 'Email(s)', 'placeholder' => 'Separar con coma', 'col' => 'col-md-8'])
                @include('components.input', ['nombre' => 'telefono', 'label' => 'Teléfono', 'col' => 'col-md-4'])
            </div>
        </div>
    </div>
</div>
