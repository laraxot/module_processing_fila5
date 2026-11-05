<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Exceptions\InactiveMachineException;
use Modules\Processing\Models\Machine;
use Modules\Processing\Models\ProcessingOrder;
use Spatie\QueueableAction\QueueableAction;

/**
 * Crea un ordine di lavorazione in bozza con codice `PO-{anno}-{progr}`.
 */
class CreateProcessingOrderAction
{
    use QueueableAction;

    private const int CODE_ATTEMPTS = 3;

    public function __construct(
        private readonly GenerateProcessingOrderCodeAction $generateCode,
    ) {}

    public function execute(
        Machine $machine,
        ?string $operationCode = null,
        ?\DateTimeInterface $plannedAt = null,
        ?string $notes = null,
    ): ProcessingOrder {
        if (! $machine->is_active) {
            throw InactiveMachineException::forMachine($machine);
        }

        $now = CarbonImmutable::now();
        $connection = (new ProcessingOrder)->getConnectionName();

        for ($attempt = 1; ; $attempt++) {
            try {
                return DB::connection($connection)->transaction(function () use ($machine, $operationCode, $plannedAt, $notes, $now): ProcessingOrder {
                    $order = new ProcessingOrder;
                    $order->fill([
                        'code' => $this->generateCode->execute($now),
                        'machine_id' => $machine->id,
                        'operation_code' => $operationCode !== null && trim($operationCode) !== '' ? trim($operationCode) : null,
                        'status' => ProcessingOrderStatusEnum::DRAFT,
                        'planned_at' => $plannedAt !== null ? CarbonImmutable::instance($plannedAt) : null,
                        'notes' => $notes,
                    ]);
                    $order->save();

                    return $order;
                });
            } catch (UniqueConstraintViolationException $e) {
                // Due creazioni concorrenti hanno letto lo stesso progressivo: si ritenta.
                if ($attempt >= self::CODE_ATTEMPTS) {
                    throw $e;
                }
            }
        }
    }
}
