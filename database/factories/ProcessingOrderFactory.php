<?php

declare(strict_types=1);

namespace Modules\Processing\Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Models\Machine;
use Modules\Processing\Models\ProcessingOrder;

/**
 * Per i test del flusso usare CreateProcessingOrderAction (codice con lock); la factory serve a fixture e liste.
 *
 * @extends Factory<ProcessingOrder>
 */
class ProcessingOrderFactory extends Factory
{
    /**
     * @var class-string<ProcessingOrder>
     */
    protected $model = ProcessingOrder::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Prefisso fuori dal pattern `PO-{anno}-` letto dal generatore: le fixture non spostano il progressivo.
            'code' => 'PO-F-'.$this->faker->unique()->numerify('########'),
            'machine_id' => Machine::factory(),
            'operation_code' => null,
            'status' => ProcessingOrderStatusEnum::DRAFT,
            'planned_at' => CarbonImmutable::now()->addDay(),
            'notes' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(static fn (array $_attributes): array => [
            'status' => ProcessingOrderStatusEnum::IN_PROGRESS,
            'started_at' => CarbonImmutable::now(),
        ]);
    }
}
