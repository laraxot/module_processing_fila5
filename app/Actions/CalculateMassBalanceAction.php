<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Modules\Processing\Datas\MassBalanceData;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * Bilancio di massa: funzione pura, nessun accesso a DB o config.
 *
 * kg arrotondati a 3 decimali (grammo), percentuale a 3 decimali.
 */
class CalculateMassBalanceAction
{
    use QueueableAction;

    /**
     * @param  array<array-key, float|int>  $inputsKg
     * @param  array<array-key, float|int>  $outputsKg
     */
    public function execute(array $inputsKg, array $outputsKg): MassBalanceData
    {
        Assert::allGreaterThanEq($inputsKg, 0);
        Assert::allGreaterThanEq($outputsKg, 0);

        $inputKg = round(array_sum(array_map(floatval(...), $inputsKg)), 3);
        $outputKg = round(array_sum(array_map(floatval(...), $outputsKg)), 3);
        $lossKg = round($inputKg - $outputKg, 3);
        $lossPct = $inputKg > 0.0 ? round($lossKg / $inputKg * 100, 3) : 0.0;

        return new MassBalanceData(
            inputKg: $inputKg,
            outputKg: $outputKg,
            lossKg: $lossKg,
            lossPct: $lossPct,
        );
    }
}
