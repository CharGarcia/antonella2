@props([
    'nombre',
    'label',
    'opciones' => [],
    'value' => null,
    'required' => false,
    'col' => 'col-md-12',
    'mostrarPrimeraOpcion' => true
])

@php
    $esColeccionDeObjetos = !empty($opciones) && is_object(reset($opciones));

    // Convertimos si es colección de objetos
    $opcionesArray = $esColeccionDeObjetos
        ? collect($opciones)->mapWithKeys(function ($item) {
            $key = $item->id ?? $item->codigo;
            $texto = $item->nombre ?? $item->descripcion ?? (string) $key;
            return [$key => $texto];
        })->all()
        : (is_array($opciones) ? $opciones : $opciones->toArray());

    // Valor actual sin seleccionar por defecto la primera opción
    $valorActual = old($nombre, $value);
@endphp

<div class="{{ $col }}">
    <label for="{{ $nombre }}">{{ $label }}</label>
    <select name="{{ $nombre }}" id="{{ $nombre }}" class="form-control select2bs4" {{ $required ? 'required' : '' }}>
        @if($mostrarPrimeraOpcion)
            <option value="" {{ is_null($valorActual) || $valorActual === '' ? 'selected' : '' }}>
                Seleccione...
            </option>
        @endif

        @foreach ($opcionesArray as $key => $text)
            @php
                $safeText = is_array($text) ? implode(' ', $text) : $text;
            @endphp
            <option value="{{ $key }}" {{ (string) $key === (string) $valorActual ? 'selected' : '' }}>
                {{ $safeText }}
            </option>
        @endforeach
    </select>
</div>
