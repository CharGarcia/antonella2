@props([
    // Select
    'nombre',
    'label' => null,
    'opciones' => [],
    'value' => null,
    'required' => false,
    'col' => 'col-md-12',
    'mostrarPrimeraOpcion' => true,

    // Botón / Modal
    'buttonIcon' => 'fas fa-plus',
    'buttonText' => '',
    'buttonClass' => 'btn btn-outline-primary',
    'buttonId' => null,
    'buttonTitle' => null,
    'modalId' => null,          // ← id del modal que abrirá
    'selectTarget' => null,     // ← selector del <select> a actualizar (ej: "#modal-cliente #id_lista_precios")
    'origin' => null,           // ← opcional, por si quieres identificar el origen (clientes, productos, etc.)
])

@php
    $opcionesArray = $opciones instanceof \Illuminate\Support\Collection ? $opciones->toArray() : (is_array($opciones) ? $opciones : []);
    $valorActual = old($nombre, $value);
@endphp

<div class="{{ $col }}">
    @if($label)
        <label for="{{ $nombre }}" class="form-label">{{ $label }}</label>
    @endif

    <div class="input-group">
        <select name="{{ $nombre }}" id="{{ $nombre }}" class="form-control select2bs4" {{ $required ? 'required' : '' }}>
            @if($mostrarPrimeraOpcion)
                <option value="" {{ is_null($valorActual) || $valorActual === '' ? 'selected' : '' }}>Seleccione...</option>
            @endif
            @foreach ($opcionesArray as $key => $text)
                @php $safeText = is_array($text) ? implode(' ', $text) : $text; @endphp
                <option value="{{ $key }}" {{ (string)$key === (string)$valorActual ? 'selected' : '' }}>
                    {{ $safeText }}
                </option>
            @endforeach
        </select>

        {{-- Botón pegado (input-group-append en BS4) --}}
        <div class="input-group-append">
            <button type="button"
                    id="{{ $buttonId }}"
                    class="{{ $buttonClass }}"
                    @if($modalId)
                        data-toggle="modal" data-target="#{{ $modalId }}"
                        data-bs-toggle="modal" data-bs-target="#{{ $modalId }}"
                    @endif
                    @if($buttonTitle) title="{{ $buttonTitle }}" @endif
                    @if($selectTarget) data-select-target="{{ $selectTarget }}" @endif
                    @if($origin) data-origin="{{ $origin }}" @endif>
                <i class="{{ $buttonIcon }}"></i>
                @if($buttonText)<span class="ms-1">{{ $buttonText }}</span>@endif
            </button>
        </div>
    </div>
</div>
