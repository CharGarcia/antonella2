@props([
    'nombre',
    'label',
    'value' => null,
    'required' => false,
    'disabled' => false,
    'col' => 'col-md-12'
])

<div class="{{ $col }}">
    <label for="{{ $nombre }}" class="form-label">{{ $label }}</label>
    <input
        type="date"
        name="{{ $nombre }}"
        id="{{ $nombre }}"
        value="{{ old($nombre, $value) }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => 'form-control']) }}
    >
</div>
