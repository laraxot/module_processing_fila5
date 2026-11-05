<?php

declare(strict_types=1);
?>
@php
    /**
     * @var list<array{node: \Modules\Processing\Datas\LineageNodeData, children: list<mixed>}> $branches
     * @var array<int, string> $orderCodes
     * @var \Modules\Processing\Filament\Pages\TraceabilityPage $page
     * @var \Closure(string, array<string, mixed>=): string $t
     */
@endphp

<ul class="space-y-2 border-s border-gray-200 ps-4 dark:border-white/10">
    @foreach ($branches as $branch)
        @php($node = $branch['node'])
        <li>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">
                <a
                    href="{{ \Modules\Processing\Filament\Pages\TraceabilityPage::getUrl(['code' => $node->code]) }}"
                    class="font-mono font-semibold text-primary-600 hover:underline dark:text-primary-400"
                >{{ $node->code }}</a>

                <x-filament::badge color="gray">
                    {{ number_format($node->attributedKg, 3, ',', '.') }} kg
                </x-filament::badge>

                <span class="text-gray-500 dark:text-gray-400">
                    {{ number_format($node->cumulativeShare * 100, 2, ',', '.') }} %
                </span>

                <a
                    href="{{ $page->getOrderUrl($node->processingOrderId) }}"
                    class="text-gray-600 hover:underline dark:text-gray-300"
                    title="{{ $t('fields.processing_order.tooltip') }}"
                >{{ $orderCodes[$node->processingOrderId] ?? '#'.$node->processingOrderId }}</a>
            </div>

            @if ($branch['children'] !== [])
                <div class="mt-2">
                    @include('processing::filament.pages.partials.lineage-tree', [
                        'branches' => $branch['children'],
                        'orderCodes' => $orderCodes,
                        'page' => $page,
                        't' => $t,
                    ])
                </div>
            @endif
        </li>
    @endforeach
</ul>
