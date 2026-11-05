<?php

declare(strict_types=1);

namespace Modules\Processing\Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Processing\Models\ProcessingInput;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Warehouse\Models\StockUnit;

/**
 * Riga di input senza movimento di magazzino: solo per fixture di lettura (genealogia, liste).
 * Il flusso reale passa da AddProcessingInputAction.
 *
 * @extends Factory<ProcessingInput>
 */
class ProcessingInputFactory extends Factory
{
    /**
     * @var class-string<ProcessingInput>
     */
    protected $model = ProcessingInput::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'processing_order_id' => ProcessingOrder::factory()->inProgress(),
            'stock_unit_id' => StockUnit::factory(),
            'quantity_kg' => $this->faker->randomFloat(3, 100, 2000),
            'consumed_at' => CarbonImmutable::now(),
        ];
    }
}
