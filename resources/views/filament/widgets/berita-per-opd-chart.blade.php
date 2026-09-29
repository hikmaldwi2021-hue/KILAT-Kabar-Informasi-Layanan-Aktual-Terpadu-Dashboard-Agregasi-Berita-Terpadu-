@php
    use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
    use Filament\Widgets\View\Components\ChartWidgetComponent;
    use Illuminate\Contracts\Support\Htmlable;

    $color = $this->getColor();
    $heading = $this->getHeading();
    $description = $this->getDescription();
    $type = $this->getType();
    $maxHeight = $this->getMaxHeight();
    $hasMaxHeight = filled($maxHeight) && $maxHeight !== '100%';
    $isEmpty = $this->isEmpty();

    $chartAccessibleLabel = trim(implode('. ', array_filter([
        $heading instanceof Htmlable ? strip_tags($heading->toHtml()) : $heading,
        $description instanceof Htmlable ? strip_tags($description->toHtml()) : $description,
    ], fn ($value): bool => filled($value))));
@endphp

<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section
        :description="$description"
        :heading="$heading"
        :collapsible="$this->isCollapsible()"
    >

        {{-- DATE PICKER --}}
        <x-slot name="afterHeader">
            <div class="flex items-center gap-2">
                <label
                    for="tanggal-mulai-berita"
                    class="whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Dari Tanggal
                </label>

                <input
                    id="tanggal-mulai-berita"
                    type="date"
                    wire:model.live="tanggalMulai"
                    max="{{ now()->format('Y-m-d') }}"
                    class="rounded-lg border-gray-300 bg-white px-3 py-2 text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                >
            </div>
        </x-slot>

        {{-- CHART --}}
        <div
            @if ($pollingInterval = $this->getPollingInterval())
                wire:poll.{{ $pollingInterval }}="updateChartData"
            @endif
            @if ($isEmpty)
                style="display: none"
            @endif
        >
            <div
                x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                wire:ignore
                data-chart-type="{{ $type }}"
                x-data="chart({
                    cachedData: @js($this->getCachedData()),
                    options: @js($this->getOptions()),
                    type: @js($type),
                })"
                {{
                    (new FilamentComponentAttributeBag)
                        ->color(ChartWidgetComponent::class, $color)
                        ->class([
                            'fi-wi-chart-frame',
                            'fi-wi-chart-canvas-ctn',
                            'fi-wi-chart-frame-no-aspect-ratio' => $hasMaxHeight,
                        ])
                }}
            >
                <canvas
                    x-ref="canvas"
                    @if (filled($chartAccessibleLabel))
                        role="img"
                        aria-label="{{ $chartAccessibleLabel }}"
                    @endif
                    @style([
                        'width: 100%',
                        'height: 100%; max-height: 100%' => ! $hasMaxHeight,
                        ('max-height: ' . e($maxHeight)) => $hasMaxHeight,
                    ])
                ></canvas>

                <span
                    x-ref="backgroundColorElement"
                    class="fi-wi-chart-bg-color"
                ></span>

                <span
                    x-ref="borderColorElement"
                    class="fi-wi-chart-border-color"
                ></span>

                <span
                    x-ref="gridColorElement"
                    class="fi-wi-chart-grid-color"
                ></span>

                <span
                    x-ref="textColorElement"
                    class="fi-wi-chart-text-color"
                ></span>
            </div>
        </div>

        {{-- EMPTY STATE --}}
        @if ($isEmpty)
            @if ($emptyState = $this->getEmptyState())
                {{ $emptyState }}
            @else
                <div
                    @class([
                        'fi-wi-chart-frame',
                        'fi-wi-chart-frame-no-aspect-ratio' => $hasMaxHeight,
                    ])
                    @style([
                        ('min-height: ' . e($maxHeight)) => $hasMaxHeight,
                    ])
                >
                    <x-filament::empty-state
                        :contained="false"
                        :description="$this->getEmptyStateDescription()"
                        :heading="$this->getEmptyStateHeading()"
                        :icon="$this->getEmptyStateIcon()"
                        icon-color="gray"
                    >
                        @if ($emptyStateActions = $this->getEmptyStateActions())
                            <x-slot name="footer">
                                <x-filament::actions
                                    :actions="$emptyStateActions"
                                    alignment="center"
                                />
                            </x-slot>
                        @endif
                    </x-filament::empty-state>
                </div>
            @endif
        @endif

    </x-filament::section>
</x-filament-widgets::widget>