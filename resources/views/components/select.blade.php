@props(['nombre', 'label', 'opciones' => [], 'required' => null])

{{-- <div class="form-group col-md-4"> --}}
    <div class="{{ $col ?? 'col-md-12' }}">
    <label for="{{ $nombre }}">{{ $label }}</label>
    <select name="{{ $nombre }}" id="{{ $nombre }}" class="form-control" {{ $required }}>
        @foreach ($opciones as $valor => $texto)
            <option value="{{ $valor }}" {{ old($nombre, $seleccionado ?? '') == $valor ? 'selected' : '' }}>
            {{ $texto }}
        </option>
        @endforeach
    </select>
</div>
