@php
    use Filament\Support\Facades\FilamentView;
    use Saade\FilamentAutograph\Forms\Components\Enums\DownloadableFormat;
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $isDisabled = $isDisabled();
        $isClearable = $isClearable();
        $isDownloadable = $isDownloadable();
        $downloadableFormats = $getDownloadableFormats();
        $downloadActionDropdownPlacement = $getDownloadActionDropdownPlacement() ?? 'bottom-start';
        $isUndoable = $isUndoable();
        $isConfirmable = $isConfirmable();
        $loadStrategy = $getLoadStrategy();

        $clearAction = $getAction('clear');
        $downloadAction = $getAction('download');
        $undoAction = $getAction('undo');
        $doneAction = $getAction('done');

        $padConfig = [
            'backgroundColor' => $getBackgroundColor(),
            'backgroundColorOnDark' => $getBackgroundColorOnDark(),
            'confirmable' => (bool) $isConfirmable,
            'disabled' => (bool) $isDisabled,
            'dotSize' => $getDotSize(),
            'exportBackgroundColor' => $getExportBackgroundColor(),
            'exportPenColor' => $getExportPenColor(),
            'filename' => (string) $getFilename(),
            'maxWidth' => $getLineMaxWidth(),
            'minDistance' => $getMinDistance(),
            'minWidth' => $getLineMinWidth(),
            'penColor' => $getPenColor(),
            'penColorOnDark' => $getPenColorOnDark(),
            'throttle' => $getThrottle(),
            'velocityFilterWeight' => $getVelocityFilterWeight(),
        ];
        $encodedConfig = rawurlencode(json_encode($padConfig));
    @endphp

    <div
        wire:ignore
        x-load="visible || event (ax-modal-opened)"
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-autograph-alpine', 'saade/filament-autograph') }}"
        x-data="signaturePadFormComponent(Object.assign(
            JSON.parse(decodeURIComponent('{{ $encodedConfig }}')),
            { state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }} }
        ))"
        style="overflow-anchor: none;"
    >
        <div class="relative w-full" style="overflow-anchor: none;">
            <canvas
                x-ref="canvas"
                wire:ignore
                x-init="if (window.setupAutographResizer) window.setupAutographResizer($refs.canvas, function() { return signaturePad; })"
                x-intersect.once="if (window.setupAutographResizer) window.setupAutographResizer($refs.canvas, function() { return signaturePad; })"
                x-on:pointerdown="if (window.setupAutographResizer) window.setupAutographResizer($refs.canvas, function() { return signaturePad; })"
                style="overflow-anchor: none;"
                @class([
                    'w-full h-44 rounded-sm border-2 border-dashed border-zinc-400 dark:border-zinc-600 bg-white dark:bg-zinc-950 shadow-inner block transition-colors',
                    'opacity-75 bg-gray-50' => $isDisabled,
                ])
            ></canvas>
            <div class="absolute top-2 right-2 text-[10px] font-mono text-zinc-400 dark:text-zinc-500 select-none pointer-events-none uppercase">
                Area Goresan Tanda Tangan
            </div>
        </div>

        <script>
            if (!window.setupAutographResizer) {
                window.setupAutographResizer = function(canvasEl, getPad) {
                    if (!canvasEl) return;
                    var sync = function() {
                        var rect = canvasEl.getBoundingClientRect();
                        if (rect.width <= 0 || rect.height <= 0) return;
                        var ratio = Math.max(window.devicePixelRatio || 1, 1);
                        var targetW = Math.round(rect.width * ratio);
                        var targetH = Math.round(rect.height * ratio);
                        if (Math.abs(canvasEl.width - targetW) > 2 || Math.abs(canvasEl.height - targetH) > 2) {
                            var pad = getPad ? getPad() : null;
                            var data = pad ? pad.toData() : null;
                            canvasEl.width = targetW;
                            canvasEl.height = targetH;
                            var ctx = canvasEl.getContext('2d');
                            if (ctx) {
                                ctx.setTransform(1, 0, 0, 1, 0, 0);
                                ctx.scale(ratio, ratio);
                            }
                            if (pad && data && data.length > 0) {
                                pad.fromData(data);
                            }
                        }
                    };
                    requestAnimationFrame(sync);
                };
            }
        </script>

        <div class="flex items-center justify-end mt-3 space-x-2">
            @if ($isClearable)
                {{ $clearAction }}
            @endif

            @if ($isUndoable)
                {{ $undoAction }}
            @endif

            @if ($isDownloadable)
                <x-filament::dropdown placement="{{ $downloadActionDropdownPlacement }}">
                    <x-slot name="trigger">
                        {{ $downloadAction }}
                    </x-slot>

                    <x-filament::dropdown.list>
                        @if (in_array(DownloadableFormat::PNG, $downloadableFormats))
                            <x-filament::dropdown.list.item
                                x-on:click="downloadAs('{{ DownloadableFormat::PNG->getMime() }}', '{{ DownloadableFormat::PNG->getExtension() }}')"
                            >
                                {{ DownloadableFormat::PNG->getLabel() }}
                            </x-filament::dropdown.list.item>
                        @endif

                        @if (in_array(DownloadableFormat::JPG, $downloadableFormats))
                            <x-filament::dropdown.list.item
                                x-on:click="downloadAs('{{ DownloadableFormat::JPG->getMime() }}', '{{ DownloadableFormat::JPG->getExtension() }}')"
                            >
                                {{ DownloadableFormat::JPG->getLabel() }}
                            </x-filament::dropdown.list.item>
                        @endif

                        @if (in_array(DownloadableFormat::SVG, $downloadableFormats))
                            <x-filament::dropdown.list.item
                                x-on:click="downloadAs('{{ DownloadableFormat::SVG->getMime() }}', '{{ DownloadableFormat::SVG->getExtension() }}')"
                            >
                                {{ DownloadableFormat::SVG->getLabel() }}
                            </x-filament::dropdown.list.item>
                        @endif
                    </x-filament::dropdown.list>
                </x-filament::dropdown>
            @endif

            @if ($isConfirmable)
                {{ $doneAction }}
            @endif
        </div>
    </div>
</x-dynamic-component>
