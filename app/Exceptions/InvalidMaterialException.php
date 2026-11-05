<?php

declare(strict_types=1);

namespace Modules\Processing\Exceptions;

use Modules\Processing\Models\ProcessingOrder;
use Modules\Warehouse\Models\StockUnit;

/**
 * Quantità o unità non valide per un input/output dell'ordine.
 */
final class InvalidMaterialException extends ProcessingException
{
    public static function nonPositiveQuantity(float $kg): self
    {
        return new self(self::translated('non_positive_quantity', ['kg' => number_format($kg, 3)]));
    }

    public static function exceedsAvailable(StockUnit $unit, float $requestedKg, float $availableKg): self
    {
        return new self(self::translated('exceeds_available', [
            'code' => $unit->code,
            'requested' => number_format($requestedKg, 3),
            'available' => number_format($availableKg, 3),
        ]));
    }

    public static function unitIsOwnOutput(ProcessingOrder $order, StockUnit $unit): self
    {
        return new self(self::translated('unit_is_own_output', [
            'code' => $unit->code,
            'order' => $order->code,
        ]));
    }

    public static function unitNotFound(string $code): self
    {
        return new self(self::translated('unit_not_found', ['code' => $code]));
    }

    public static function emptyInputs(): self
    {
        return new self(self::translated('empty_inputs'));
    }
}
