<?php

declare(strict_types=1);

namespace Modules\Processing\Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Orchestratore Processing.
 *
 * Solo dati anagrafici demo: ordini, input/output e genealogia nascono dalle Actions
 * (movimentano il magazzino) e non si seminano.
 */
class ProcessingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MachineSeeder::class,
        ]);
    }
}
