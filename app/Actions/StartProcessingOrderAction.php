<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Modules\Processing\Actions\Concerns\GuardsOrderState;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Exceptions\InactiveMachineException;
use Modules\Processing\Models\ProcessingOrder;
use Spatie\QueueableAction\QueueableAction;

/**
 * Avvia la lavorazione: da qui l'ordine accetta input e output.
 */
class StartProcessingOrderAction
{
    use GuardsOrderState;
    use QueueableAction;

    public function execute(ProcessingOrder $order): ProcessingOrder
    {
        return DB::connection($order->getConnectionName())->transaction(function () use ($order): ProcessingOrder {
            $locked = $this->lockOrder($order);
            $this->assertTransition($locked, ProcessingOrderStatusEnum::IN_PROGRESS);

            if (! $locked->machine->is_active) {
                throw InactiveMachineException::forMachine($locked->machine);
            }

            $locked->status = ProcessingOrderStatusEnum::IN_PROGRESS;
            $locked->started_at = CarbonImmutable::now();
            $locked->save();

            return $locked;
        });
    }
}
