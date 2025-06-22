{{-- @props(['disabled' => false])
<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>
 --}}

 @props(['nombre', 'label', 'type' => 'text', 'placeholder' => '', 'required' => null])

<div class="{{ $col ?? 'col-md-12' }}">
    <label for="{{ $nombre }}">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $nombre }}" id="{{ $nombre }}"
           class="form-control" value="{{ old($nombre) }}"
           placeholder="{{ $placeholder }}" {{ $required }}>
</div>
