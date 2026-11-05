<?php

declare(strict_types=1);

namespace Modules\Processing\Exceptions;

use Modules\Processing\Datas\MassBalanceData;
use Modules\Processing\Models\ProcessingOrder;

/**
 * La perdita di massa supera `processing.max_loss_pct`: la chiusura richiede `$force = true`.
 */
final class ExcessiveMassLossException extends ProcessingException
{
    public function __construct(
        string $message,
        public readonly MassBalanceData $balance,
        public readonly float $maxLossPct,
    ) {
        parent::__construct($message);
    }

    public static function forOrder(ProcessingOrder $order, MassBalanceData $balance, float $maxLossPct): self
    {
        return new self(
            self::translated('excessive_loss', [
                'code' => $order->code,
                'loss_pct' => number_format($balance->lossPct, 2),
                'max_loss_pct' => number_format($maxLossPct, 2),
            ]),
            $balance,
            $maxLossPct,
        );
    }
}
