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
    @endphp

    <div
        wire:ignore
        @if (FilamentView::hasSpaMode())
            {{-- format-ignore-start --}}x-load="visible || event (ax-modal-opened)"{{-- format-ignore-end --}}
        @else
            x-load
        @endif
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-autograph-alpine', 'saade/filament-autograph') }}"
        x-data="signaturePadFormComponent({
            backgroundColor: @js($getBackgroundColor()),
            backgroundColorOnDark: @js($getBackgroundColorOnDark()),
            confirmable: @js($isConfirmable),
            disabled: @js($isDisabled),
            dotSize: {{ $getDotSize() }},
            exportBackgroundColor: @js($getExportBackgroundColor()),
            exportPenColor: @js($getExportPenColor()),
            filename: '{{ $getFilename() }}',
            maxWidth: {{ $getLineMaxWidth() }},
            minDistance: {{ $getMinDistance() }},
            minWidth: {{ $getLineMinWidth() }},
            penColor: @js($getPenColor()),
            penColorOnDark: @js($getPenColorOnDark()),
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }},
            throttle: {{ $getThrottle() }},
            velocityFilterWeight: {{ $getVelocityFilterWeight() }},
        })"
    >
        <div class="relative w-full">
            <canvas
                x-ref="canvas"
                wire:ignore
                x-init="window.setupAutographResizer($refs.canvas, () => signaturePad)"
                x-on:pointerdown="window.setupAutographResizer($refs.canvas, () => signaturePad)"
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
                    const sync = function() {
                        const rect = canvasEl.getBoundingClientRect();
                        if (rect.width <= 0 || rect.height <= 0) return;
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        const targetW = Math.round(rect.width * ratio);
                        const targetH = Math.round(rect.height * ratio);
                        if (canvasEl.width !== targetW || canvasEl.height !== targetH) {
                            const pad = getPad ? getPad() : null;
                            const data = pad ? pad.toData() : null;
                            canvasEl.width = targetW;
                            canvasEl.height = targetH;
                            const ctx = canvasEl.getContext('2d');
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
                    setTimeout(sync, 150);
                    if (window.ResizeObserver && !canvasEl._hasResizeObserver) {
                        canvasEl._hasResizeObserver = true;
                        new ResizeObserver(sync).observe(canvasEl);
                    }
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
