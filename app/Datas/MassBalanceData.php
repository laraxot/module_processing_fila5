<?php

declare(strict_types=1);

namespace Modules\Processing\Datas;

use Spatie\LaravelData\Data;

/**
 * Bilancio di massa di un ordine di lavorazione.
 *
 * `lossKg = inputKg - outputKg` (negativo se l'output pesa più dell'input, es. umidità assorbita);
 * `lossPct = lossKg / inputKg * 100` (0 se non ci sono input).
 */
final class MassBalanceData extends Data
{
    public function __construct(
        public readonly float $inputKg,
        public readonly float $outputKg,
        public readonly float $lossKg,
        public readonly float $lossPct,
    ) {}

    public function exceeds(float $maxLossPct): bool
    {
        return $this->lossPct > $maxLossPct;
    }
}
