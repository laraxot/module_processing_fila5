<?php

declare(strict_types=1);

namespace Modules\Processing\Exceptions;

use Modules\Processing\Models\ProcessingOrder;

/**
 * Un ordine con materiale già movimentato non si annulla:
 * gli input sono usciti dal magazzino, gli output sono già unità registrate.
 */
final class OrderNotCancellableException extends ProcessingException
{
    public static function hasConsumedInputs(ProcessingOrder $order, int $count): self
    {
        return new self(self::translated('has_consumed_inputs', [
            'code' => $order->code,
            'count' => $count,
        ]));
    }

    public static function hasRegisteredOutputs(ProcessingOrder $order, int $count): self
    {
        return new self(self::translated('has_registered_outputs', [
            'code' => $order->code,
            'count' => $count,
        ]));
    }

    public static function missingReason(ProcessingOrder $order): self
    {
        return new self(self::translated('missing_cancel_reason', ['code' => $order->code]));
    }
}
