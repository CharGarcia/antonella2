@props(['nombre', 'label', 'multiple' => false])

<div class="form-group col-md-6">
    <label for="{{ $nombre }}">{{ $label }}</label>
    <input type="file" name="{{ $nombre }}" id="{{ $nombre }}"
           class="form-control-file" {{ $multiple ? 'multiple' : '' }}>
</div>
