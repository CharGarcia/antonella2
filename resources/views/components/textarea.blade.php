@props(['nombre', 'label', 'rows' => 3, 'required' => null])

<div class="form-group col-md-6">
    <label for="{{ $nombre }}">{{ $label }}</label>
    <textarea name="{{ $nombre }}" id="{{ $nombre }}" rows="{{ $rows }}" class="form-control" {{ $required }}>{{ old($nombre) }}</textarea>
</div>
