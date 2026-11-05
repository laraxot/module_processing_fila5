<?php

declare(strict_types=1);

namespace Modules\Processing\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Processing\Enums\MachineTypeEnum;
use Modules\Processing\Models\Machine;

/**
 * Macchine demo: trituratore, vaglio, separatore magnetico. Idempotente (per codice).
 */
class MachineSeeder extends Seeder
{
    public function run(): void
    {
        $machines = [
            ['code' => 'TRI-01', 'name' => 'Trituratore primario', 'type' => MachineTypeEnum::SHREDDER, 'capacity_kg_h' => 12000.0],
            ['code' => 'VAG-01', 'name' => 'Vaglio rotante', 'type' => MachineTypeEnum::SCREEN, 'capacity_kg_h' => 15000.0],
            ['code' => 'MAG-01', 'name' => 'Separatore magnetico', 'type' => MachineTypeEnum::MAGNETIC_SEPARATOR, 'capacity_kg_h' => 10000.0],
        ];

        foreach ($machines as $machine) {
            Machine::query()->updateOrCreate(
                ['code' => $machine['code']],
                [
                    'name' => $machine['name'],
                    'type' => $machine['type'],
                    'capacity_kg_h' => $machine['capacity_kg_h'],
                    'is_active' => true,
                ],
            );
        }
    }
}
