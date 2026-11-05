<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Modules\Processing\Actions\Concerns\GuardsOrderState;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Exceptions\ExcessiveMassLossException;
use Modules\Processing\Exceptions\IncompleteProcessingOrderException;
use Modules\Processing\Models\LineageLink;
use Modules\Processing\Models\ProcessingInput;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Processing\Models\ProcessingOutput;
use Spatie\QueueableAction\QueueableAction;

/**
 * Chiude l'ordine: bilancio di massa, genealogia, snapshot.
 *
 * - richiede ≥1 input e ≥1 output;
 * - bilancio con CalculateMassBalanceAction; se `lossPct` > `processing.max_loss_pct`
 *   lancia ExcessiveMassLossException, salvo `$force`;
 * - scrive un LineageLink per ogni coppia output × input (share e attributed_kg da CalculateLineageSharesAction);
 * - salva lo snapshot `input_kg`, `output_kg`, `loss_kg`, `loss_pct`, stato `completed`.
 *
 * Tocca solo tabelle di Processing: una transazione sulla sua connessione.
 */
class CompleteProcessingOrderAction
{
    use GuardsOrderState;
    use QueueableAction;

    public function __construct(
        private readonly CalculateMassBalanceAction $calculateMassBalance,
        private readonly CalculateLineageSharesAction $calculateLineageShares,
    ) {}

    public function execute(ProcessingOrder $order, bool $force = false): ProcessingOrder
    {
        return DB::connection($order->getConnectionName())->transaction(function () use ($order, $force): ProcessingOrder {
            $locked = $this->lockOrder($order);
            $this->assertTransition($locked, ProcessingOrderStatusEnum::COMPLETED);

            $inputsKgByUnit = $this->sumByUnit($locked->inputs()->orderBy('id')->get()->all());
            $outputsKgByUnit = $this->sumByUnit($locked->outputs()->orderBy('id')->get()->all());

            if ($inputsKgByUnit === []) {
                throw IncompleteProcessingOrderException::missingInputs($locked);
            }
            if ($outputsKgByUnit === []) {
                throw IncompleteProcessingOrderException::missingOutputs($locked);
            }

            $balance = $this->calculateMassBalance->execute($inputsKgByUnit, $outputsKgByUnit);
            $maxLossPct = config()->float('processing.max_loss_pct', 15.0);

            if (! $force && $balance->exceeds($maxLossPct)) {
                throw ExcessiveMassLossException::forOrder($locked, $balance, $maxLossPct);
            }

            foreach ($this->calculateLineageShares->execute($inputsKgByUnit, $outputsKgByUnit) as $link) {
                $lineage = new LineageLink;
                $lineage->fill([
                    'processing_order_id' => $locked->id,
                    'parent_stock_unit_id' => $link->parentStockUnitId,
                    'child_stock_unit_id' => $link->childStockUnitId,
                    'share' => $link->share,
                    'attributed_kg' => $link->attributedKg,
                ]);
                $lineage->save();
            }

            $locked->fill([
                'status' => ProcessingOrderStatusEnum::COMPLETED,
                'completed_at' => CarbonImmutable::now(),
                'input_kg' => $balance->inputKg,
                'output_kg' => $balance->outputKg,
                'loss_kg' => $balance->lossKg,
                'loss_pct' => $balance->lossPct,
            ]);
            $locked->save();

            return $locked;
        });
    }

    /**
     * @param  array<int, ProcessingInput|ProcessingOutput>  $rows
     * @return array<int, float> stock_unit_id => kg
     */
    private function sumByUnit(array $rows): array
    {
        $totals = [];
        foreach ($rows as $row) {
            $unitId = $row->stock_unit_id;
            $totals[$unitId] = round(($totals[$unitId] ?? 0.0) + (float) $row->quantity_kg, 3);
        }

        return $totals;
    }
}
