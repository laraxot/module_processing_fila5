<?php

declare(strict_types=1);

namespace Modules\Processing\Exceptions;

use Modules\Processing\Models\ProcessingOrder;

/**
 * L'ordine non ha il materiale minimo per essere chiuso (≥1 input e ≥1 output).
 */
final class IncompleteProcessingOrderException extends ProcessingException
{
    public static function missingInputs(ProcessingOrder $order): self
    {
        return new self(self::translated('missing_inputs', ['code' => $order->code]));
    }

    public static function missingOutputs(ProcessingOrder $order): self
    {
        return new self(self::translated('missing_outputs', ['code' => $order->code]));
    }
}
