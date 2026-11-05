<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Modules\Processing\Datas\LineageShareData;
use Modules\Processing\Exceptions\InvalidMaterialException;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * Archi di genealogia di un ordine: funzione pura.
 *
 * Per ogni output × input: `share = input_kg / totale_input`, `attributed_kg = output_kg * share`.
 * Più consumi della stessa unità nello stesso ordine si sommano (un solo arco per coppia).
 */
class CalculateLineageSharesAction
{
    use QueueableAction;

    /**
     * @param  array<int, float>  $inputsKgByUnit  stock_unit_id => kg consumati
     * @param  array<int, float>  $outputsKgByUnit  stock_unit_id => kg prodotti
     * @return list<LineageShareData>
     */
    public function execute(array $inputsKgByUnit, array $outputsKgByUnit): array
    {
        Assert::allGreaterThanEq($inputsKgByUnit, 0);
        Assert::allGreaterThanEq($outputsKgByUnit, 0);

        $totalInput = array_sum($inputsKgByUnit);
        if ($totalInput <= 0.0) {
            throw InvalidMaterialException::emptyInputs();
        }

        $links = [];
        foreach ($outputsKgByUnit as $childId => $outputKg) {
            foreach ($inputsKgByUnit as $parentId => $inputKg) {
                $share = $inputKg / $totalInput;
                $links[] = new LineageShareData(
                    parentStockUnitId: $parentId,
                    childStockUnitId: $childId,
                    share: round($share, 6),
                    attributedKg: round($outputKg * $share, 3),
                );
            }
        }

        return $links;
    }
}
