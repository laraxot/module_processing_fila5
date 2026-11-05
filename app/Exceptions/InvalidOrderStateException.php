<?php

declare(strict_types=1);

namespace Modules\Processing\Exceptions;

use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Models\ProcessingOrder;

/**
 * L'ordine non è nello stato richiesto dall'operazione (transizione non consentita o materiale non accettato).
 */
final class InvalidOrderStateException extends ProcessingException
{
    public static function transition(ProcessingOrder $order, ProcessingOrderStatusEnum $to): self
    {
        return new self(self::translated('invalid_transition', [
            'code' => $order->code,
            'from' => (string) $order->status->getLabel(),
            'to' => (string) $to->getLabel(),
        ]));
    }

    public static function notAcceptingMaterial(ProcessingOrder $order): self
    {
        return new self(self::translated('not_accepting_material', [
            'code' => $order->code,
            'status' => (string) $order->status->getLabel(),
        ]));
    }
}
