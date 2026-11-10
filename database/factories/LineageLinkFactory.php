<?php

declare(strict_types=1);

namespace Modules\Processing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Processing\Models\LineageLink;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeIntCastAction;

/**
 * Arco di genealogia per fixture di lettura (test della tracciabilità).
 * Il flusso reale lo scrive CompleteProcessingOrderAction.
 *
 * @extends Factory<LineageLink>
 */
class LineageLinkFactory extends Factory
{
    /**
     * @var class-string<LineageLink>
     */
    protected $model = LineageLink::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'processing_order_id' => ProcessingOrder::factory(),
            'parent_stock_unit_id' => StockUnit::factory(),
            'child_stock_unit_id' => StockUnit::factory(),
            'share' => 1.0,
            'attributed_kg' => $this->faker->randomFloat(3, 100, 2000),
        ];
    }

    public function between(StockUnit $parent, StockUnit $child, float $share, float $attributedKg): static
    {
        return $this->state(fn (array $_attributes): array => [
            'parent_stock_unit_id' => SafeIntCastAction::cast($parent->getKey()),
            'child_stock_unit_id' => SafeIntCastAction::cast($child->getKey()),
            'share' => $share,
            'attributed_kg' => $attributedKg,
        ]);
    }
}
