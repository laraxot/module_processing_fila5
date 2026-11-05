<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Carbon\CarbonImmutable;
use Modules\Processing\Actions\Concerns\GuardsOrderState;
use Modules\Processing\Actions\Concerns\RunsInModuleTransactions;
use Modules\Processing\Enums\OutputKindEnum;
use Modules\Processing\Exceptions\InvalidMaterialException;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Processing\Models\ProcessingOutput;
use Modules\Warehouse\Actions\CreateStockUnitAction;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Registra un'unità prodotta dall'ordine: la crea in Warehouse con movimento d'origine `production`
 * e reference all'ordine, poi la lega all'ordine come output.
 *
 * La genealogia (LineageLink) si scrive alla chiusura, quando il totale degli input è definitivo.
 */
class RegisterProcessingOutputAction
{
    use GuardsOrderState;
    use QueueableAction;
    use RunsInModuleTransactions;

    public function __construct(
        private readonly CreateStockUnitAction $createStockUnit,
    ) {}

    public function execute(
        ProcessingOrder $order,
        StockUnitData $unitData,
        OutputKindEnum $kind = OutputKindEnum::PRODUCT,
    ): ProcessingOutput {
        $quantityKg = round($unitData->netWeightKg, 3);
        if ($quantityKg <= 0.0) {
            throw InvalidMaterialException::nonPositiveQuantity($quantityKg);
        }

        return $this->inModuleTransaction(function () use ($order, $unitData, $kind, $quantityKg): ProcessingOutput {
            $locked = $this->lockOrder($order);
            $this->assertAcceptsMaterial($locked);

            $unit = $this->createStockUnit->execute($unitData, MovementTypeEnum::PRODUCTION, $locked);

            $output = new ProcessingOutput;
            $output->fill([
                'processing_order_id' => $locked->id,
                'stock_unit_id' => SafeIntCastAction::cast($unit->getKey()),
                'kind' => $kind,
                'quantity_kg' => $quantityKg,
                'produced_at' => CarbonImmutable::now(),
            ]);
            $output->save();

            return $output;
        });
    }
}
