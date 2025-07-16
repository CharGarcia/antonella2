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
    <div class="input-group" id="{{ $nombre }}Picker">
        <input
            type="text"
            name="{{ $nombre }}"
            id="{{ $nombre }}"
            value="{{ old($nombre, $value) }}"
            class="form-control datetimepicker-input"
            autocomplete="off"
            @if($required) required @endif
            @if($disabled) disabled @endif
        >
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" id="{{ $nombre }}Icon">
                <i class="fa fa-calendar"></i>
            </button>
        </div>
    </div>
</div>
