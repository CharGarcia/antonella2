@props(['nombre', 'label', 'required' => null])

<div class="form-group col-md-4">
    <label for="{{ $nombre }}">{{ $label }}</label>
    <input type="date" name="{{ $nombre }}" id="{{ $nombre }}"
           class="form-control" value="{{ old($nombre) }}" {{ $required }}>
</div>
