<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Modules\Processing\Actions\Concerns\GuardsOrderState;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Exceptions\OrderNotCancellableException;
use Modules\Processing\Models\ProcessingOrder;
use Spatie\QueueableAction\QueueableAction;

/**
 * Annulla un ordine in bozza o avviato, purché nessun materiale sia stato movimentato.
 *
 * SPEC: "solo se nessun input consumato". Si rifiuta anche con output registrati:
 * quelle StockUnit esistono già in Warehouse con movimento `production` e un ordine annullato
 * le lascerebbe senza origine. Vanno stornate in Warehouse (VoidStockUnitAction) prima.
 */
class CancelProcessingOrderAction
{
    use GuardsOrderState;
    use QueueableAction;

    public function execute(ProcessingOrder $order, string $reason): ProcessingOrder
    {
        return DB::connection($order->getConnectionName())->transaction(function () use ($order, $reason): ProcessingOrder {
            $locked = $this->lockOrder($order);
            $this->assertTransition($locked, ProcessingOrderStatusEnum::CANCELLED);

            $reason = trim($reason);
            if ($reason === '') {
                throw OrderNotCancellableException::missingReason($locked);
            }

            $inputs = $locked->inputs()->count();
            if ($inputs > 0) {
                throw OrderNotCancellableException::hasConsumedInputs($locked, $inputs);
            }

            $outputs = $locked->outputs()->count();
            if ($outputs > 0) {
                throw OrderNotCancellableException::hasRegisteredOutputs($locked, $outputs);
            }

            $locked->fill([
                'status' => ProcessingOrderStatusEnum::CANCELLED,
                'cancelled_at' => CarbonImmutable::now(),
                'cancel_reason' => $reason,
            ]);
            $locked->save();

            return $locked;
        });
    }
}
