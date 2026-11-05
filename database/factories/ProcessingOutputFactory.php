<?php

declare(strict_types=1);

namespace Modules\Processing\Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Processing\Enums\OutputKindEnum;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Processing\Models\ProcessingOutput;
use Modules\Warehouse\Models\StockUnit;

/**
 * Riga di output senza movimento di magazzino: solo per fixture di lettura.
 * Il flusso reale passa da RegisterProcessingOutputAction.
 *
 * @extends Factory<ProcessingOutput>
 */
class ProcessingOutputFactory extends Factory
{
    /**
     * @var class-string<ProcessingOutput>
     */
    protected $model = ProcessingOutput::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'processing_order_id' => ProcessingOrder::factory()->inProgress(),
            'stock_unit_id' => StockUnit::factory(),
            'kind' => OutputKindEnum::PRODUCT,
            'quantity_kg' => $this->faker->randomFloat(3, 100, 2000),
            'produced_at' => CarbonImmutable::now(),
        ];
    }
}
