<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Carbon\CarbonImmutable;
use Modules\Processing\Actions\Concerns\GuardsOrderState;
use Modules\Processing\Actions\Concerns\RunsInModuleTransactions;
use Modules\Processing\Exceptions\InvalidMaterialException;
use Modules\Processing\Models\ProcessingInput;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Warehouse\Actions\ConsumeStockUnitAction;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Carica un'unità di magazzino nell'ordine: la consuma in Warehouse (movimento `consumption`
 * con reference all'ordine) e registra l'input.
 *
 * `$kg` null = intera unità. Le regole sullo stato dell'unità (consumabile o no) sono di Warehouse.
 */
class AddProcessingInputAction
{
    use GuardsOrderState;
    use QueueableAction;
    use RunsInModuleTransactions;

    public function __construct(
        private readonly ConsumeStockUnitAction $consumeStockUnit,
    ) {}

    public function execute(ProcessingOrder $order, StockUnit $unit, ?float $kg = null): ProcessingInput
    {
        return $this->inModuleTransaction(function () use ($order, $unit, $kg): ProcessingInput {
            $locked = $this->lockOrder($order);
            $this->assertAcceptsMaterial($locked);

            $unitId = SafeIntCastAction::cast($unit->getKey());
            $freshUnit = StockUnit::query()->lockForUpdate()->findOrFail($unitId);

            if ($locked->outputs()->where('stock_unit_id', $unitId)->exists()) {
                throw InvalidMaterialException::unitIsOwnOutput($locked, $freshUnit);
            }

            $availableKg = round((float) $freshUnit->net_weight_kg, 3);
            $quantityKg = round($kg ?? $availableKg, 3);

            if ($quantityKg <= 0.0) {
                throw InvalidMaterialException::nonPositiveQuantity($quantityKg);
            }
            if ($quantityKg > $availableKg) {
                throw InvalidMaterialException::exceedsAvailable($freshUnit, $quantityKg, $availableKg);
            }

            $this->consumeStockUnit->execute($freshUnit, $quantityKg, $locked);

            $input = new ProcessingInput;
            $input->fill([
                'processing_order_id' => $locked->id,
                'stock_unit_id' => $unitId,
                'quantity_kg' => $quantityKg,
                'consumed_at' => CarbonImmutable::now(),
            ]);
            $input->save();

            return $input;
        });
    }
}
