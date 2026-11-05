<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\MachineResource\Pages;

use Modules\Processing\Filament\Resources\MachineResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseListRecords;

class ListMachines extends XotBaseListRecords
{
    protected static string $resource = MachineResource::class;
}
