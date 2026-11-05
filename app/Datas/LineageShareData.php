<?php

declare(strict_types=1);

namespace Modules\Processing\Datas;

use Spatie\LaravelData\Data;

/**
 * Un arco di genealogia calcolato (non ancora persistito) da CalculateLineageSharesAction.
 */
final class LineageShareData extends Data
{
    public function __construct(
        public readonly int $parentStockUnitId,
        public readonly int $childStockUnitId,
        public readonly float $share,
        public readonly float $attributedKg,
    ) {}
}
