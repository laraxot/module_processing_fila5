<?php

declare(strict_types=1);

namespace Modules\Processing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Processing\Enums\MachineTypeEnum;
use Modules\Processing\Models\Machine;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * @extends Factory<Machine>
 */
class MachineFactory extends Factory
{
    /**
     * @var class-string<Machine>
     */
    protected $model = Machine::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'M-'.$this->faker->unique()->numerify('####'),
            'name' => ucfirst(SafeStringCastAction::cast($this->faker->words(2, true))),
            'type' => $this->faker->randomElement(MachineTypeEnum::cases()),
            'location_ref_id' => null,
            'capacity_kg_h' => $this->faker->randomFloat(3, 500, 20000),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(static fn (array $_attributes): array => ['is_active' => false]);
    }

    public function ofType(MachineTypeEnum $type): static
    {
        return $this->state(static fn (array $_attributes): array => ['type' => $type]);
    }
}
