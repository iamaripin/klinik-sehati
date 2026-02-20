@props([
    'id',
    'title' => 'Window',
    'width' => 600,
    'height' => 400,
    'position' => 'center'
])

<div id="{{ $id }}"
     wire:ignore.self
     data-position="{{ $position }}"
     class="cw-window"
     style="width: {{ $width }}px; height: {{ $height }}px; display:none; position:fixed;">

    <div class="cw-header">
        <div class="cw-title">{{ $title }}</div>

        <div class="cw-controls">
            <button type="button" class="cw-min"></button>
            <button type="button" class="cw-max"></button>
            <button type="button" class="cw-close"></button>
        </div>
    </div>

    <div class="cw-body">
        {{ $slot }}
    </div>

    <div class="cw-resizer"></div>
</div>
