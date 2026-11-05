<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\MachineResource\Pages;

use Modules\Processing\Filament\Resources\MachineResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseCreateRecord;

class CreateMachine extends XotBaseCreateRecord
{
    protected static string $resource = MachineResource::class;
}
