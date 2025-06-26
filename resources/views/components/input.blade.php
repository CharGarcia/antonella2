{{-- @props(['disabled' => false])
<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>
 --}}
@props([
    'nombre',
    'label',
    'type' => 'text',
    'placeholder' => '',
    'required' => null,
    'step' => null,
    'min' => null,
    'value' => null,
    'readonly' => false,
    'col' => 'col-md-12'
])

<div class="{{ $col }}">
    <label for="{{ $nombre }}" class="form-label">{{ $label }}</label>
    <input
        type="{{ $type }}"
        name="{{ $nombre }}"
        id="{{ $nombre }}"
        value="{{ old($nombre, $value) }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if(!is_null($step)) step="{{ $step }}" @endif
        @if(!is_null($min)) min="{{ $min }}" @endif
        @if($readonly) readonly @endif
        {{ $attributes->merge(['class' => 'form-control']) }}
    >
</div>

