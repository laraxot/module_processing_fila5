<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Modules\Processing\Models\ProcessingOrder;
use Spatie\QueueableAction\QueueableAction;

/**
 * Prossimo codice `PO-{anno}-{progressivo 6 cifre}`.
 *
 * Va chiamata DENTRO una transazione: legge l'ultimo codice dell'anno con `lockForUpdate`
 * (su InnoDB il lock di intervallo blocca anche gli inserimenti concorrenti a tabella vuota).
 * L'indice unique su `code` resta l'ultima garanzia; CreateProcessingOrderAction ritenta in caso di collisione.
 */
class GenerateProcessingOrderCodeAction
{
    use QueueableAction;

    public function execute(\DateTimeInterface $at): string
    {
        $prefix = sprintf('PO-%04d-', (int) $at->format('Y'));

        $last = ProcessingOrder::query()
            ->where('code', 'like', $prefix.'%')
            ->orderByDesc('code')
            ->lockForUpdate()
            ->value('code');

        $next = is_string($last) ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
