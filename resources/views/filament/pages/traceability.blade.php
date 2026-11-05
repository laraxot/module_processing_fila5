<?php

declare(strict_types=1);
?>
@php
    /** @var \Modules\Processing\Filament\Pages\TraceabilityPage $this */
    $unit = $this->getStockUnit();
    $trace = $this->getTrace();
    $orderCodes = $trace['orderCodes'];
    $t = static fn (string $key, array $params = []): string => \Modules\Processing\Filament\Pages\TraceabilityPage::trans($key, false, $params);
@endphp

<x-filament-panels::page>
    <form wire:submit="search" class="flex flex-col gap-4 sm:flex-row sm:items-end">
        <div class="flex-1">
            {{ $this->form }}
        </div>
        <x-filament::actions :actions="$this->getFormActions()" />
    </form>

    @if ($this->notFound)
        <x-filament::section icon="heroicon-o-x-circle" icon-color="danger">
            {{ $t('messages.not_found', ['code' => $this->code]) }}
        </x-filament::section>
    @endif

    @if ($unit !== null)
        <x-filament::section icon="heroicon-o-cube">
            <x-slot name="heading">{{ $unit->code }}</x-slot>
            <x-slot name="description">
                {{ $t('messages.unit_weight') }}: {{ number_format((float) $unit->net_weight_kg, 3, ',', '.') }} kg
            </x-slot>
        </x-filament::section>

        <div class="grid gap-6 lg:grid-cols-2">
            @foreach ([
                'upstream' => $trace['upstream'],
                'downstream' => $trace['downstream'],
            ] as $direction => $tree)
                <x-filament::section :icon="$direction === 'upstream' ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-share'">
                    <x-slot name="heading">{{ $t('sections.'.$direction.'.heading') }}</x-slot>
                    <x-slot name="description">{{ $t('sections.'.$direction.'.description') }}</x-slot>

                    @if ($tree === [])
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('messages.empty_'.$direction) }}</p>
                    @else
                        @include('processing::filament.pages.partials.lineage-tree', [
                            'branches' => $tree,
                            'orderCodes' => $orderCodes,
                            'page' => $this,
                            't' => $t,
                        ])
                    @endif
                </x-filament::section>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
