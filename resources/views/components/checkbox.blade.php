{{-- <input type="checkbox" {!! $attributes->merge(['class' => 'rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500']) !!}> --}}
@props(['nombre', 'label'])

<div class="form-group col-md-4 form-check">
    <input type="checkbox" name="{{ $nombre }}" id="{{ $nombre }}" class="form-check-input" {{ old($nombre) ? 'checked' : '' }}>
    <label class="form-check-label" for="{{ $nombre }}">{{ $label }}</label>
</div>
