
@php
    $nombre = $nombre ?? '';
    $label = $label ?? '';
    $type = $type ?? 'text';
    $placeholder = $placeholder ?? '';
    $required = $required ?? false;
    $step = $step ?? null;
    $min = $min ?? null;
    $value = $value ?? null;
    $readonly = $readonly ?? false;
    $col = $col ?? 'col-md-12';
    $claseExtra = $claseExtra ?? ''; // 👈 clase adicional opcional, como 'text-end'
@endphp

<div class="{{ $col }}">
    <label for="{{ $nombre }}" class="form-label">{{ $label }}</label>
    <input
        type="{{ $type }}"
        name="{{ $nombre }}"
        id="{{ $nombre }}"
        value="{{ old($nombre, $value) }}"
        placeholder="{{ $placeholder }}"
        class="form-control {{ $claseExtra }}"
        @if($required) required @endif
        @if(!is_null($step)) step="{{ $step }}" @endif
        @if(!is_null($min)) min="{{ $min }}" @endif
        @if($readonly) readonly @endif
    >
</div>
