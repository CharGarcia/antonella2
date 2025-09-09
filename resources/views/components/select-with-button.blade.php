{{-- resources/views/components/select-with-button.blade.php --}}
@props([
    // Select
    'nombre',
    'label' => null,
    'opciones' => [],     // Puede ser array [id => texto], Collection, o lista de objetos (id/nombre|descripcion)
    'value' => null,
    'required' => false,
    'col' => 'col-md-12',
    'mostrarPrimeraOpcion' => true,

    // Botón
    'buttonIcon' => 'fas fa-plus',
    'buttonText' => '',                 // texto opcional junto al ícono
    'buttonClass' => 'btn btn-outline-primary',
    'buttonId' => null,
    'buttonTitle' => null,              // tooltip nativo
    'modalId' => null,                  // si lo pasas, abrirá el modal con ese ID (Bootstrap 5)
])

@php
    // Normalizar opciones a array [key => text]
    if ($opciones instanceof \Illuminate\Support\Collection) {
        $opcionesArray = $opciones->toArray();
    } else {
        $opcionesArray = is_array($opciones) ? $opciones : (array) $opciones;
    }

    // Si es lista de objetos (o arrays) sin llaves, intentar mapear id=>texto
    $first = is_array($opcionesArray) ? reset($opcionesArray) : null;
    $esListaObjetos = $first && (is_object($first) || is_array($first));

    if ($esListaObjetos) {
        $opcionesArray = collect($opcionesArray)->mapWithKeys(function ($item) {
            $id    = is_array($item) ? ($item['id'] ?? $item['codigo'] ?? null) : ($item->id ?? $item->codigo ?? null);
            $texto = is_array($item)
                ? ($item['nombre'] ?? $item['descripcion'] ?? (string) $id)
                : ($item->nombre ?? $item->descripcion ?? (string) $id);
            return $id !== null ? [$id => $texto] : [];
        })->all();
    }

    $valorActual = old($nombre, $value);
@endphp

<div class="{{ $col }}">
    @if($label)
        <label for="{{ $nombre }}" class="form-label">{{ $label }}</label>
    @endif

    <div class="input-group">
        {{-- SELECT (compatible con Select2 si ya lo inicializas afuera) --}}
        <select name="{{ $nombre }}" id="{{ $nombre }}"
                class="form-control select2bs4"
                {{ $required ? 'required' : '' }}>
            @if($mostrarPrimeraOpcion)
                <option value="" {{ is_null($valorActual) || $valorActual === '' ? 'selected' : '' }}>
                    Seleccione...
                </option>
            @endif

            @foreach ($opcionesArray as $key => $text)
                @php $safeText = is_array($text) ? implode(' ', $text) : $text; @endphp
                <option value="{{ $key }}" {{ (string) $key === (string) $valorActual ? 'selected' : '' }}>
                    {{ $safeText }}
                </option>
            @endforeach
        </select>

        {{-- ADDON tipo datepicker: botón pegado dentro del input-group --}}
        <span class="input-group-text p-0">
            {{-- ADDON pegado (Bootstrap 4) --}}
            <div class="input-group-append">
            <button type="button"
                    id="{{ $buttonId }}"
                    class="{{ $buttonClass }}"
                    @if($modalId)
                        data-toggle="modal" data-target="#{{ $modalId }}"
                        data-bs-toggle="modal" data-bs-target="#{{ $modalId }}"
                    @endif
                    @if($buttonTitle) title="{{ $buttonTitle }}" @endif>
                <i class="{{ $buttonIcon }}"></i>
                @if($buttonText)<span class="ms-1">{{ $buttonText }}</span>@endif
            </button>
            </div>
        </span>
    </div>
</div>
