<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources;

use Filament\Resources\RelationManagers\RelationManager;
use Modules\Processing\Filament\Resources\MachineResource\Pages\CreateMachine;
use Modules\Processing\Filament\Resources\MachineResource\Pages\EditMachine;
use Modules\Processing\Filament\Resources\MachineResource\Pages\ListMachines;
use Modules\Processing\Filament\Resources\MachineResource\Pages\ViewMachine;
use Modules\Processing\Models\Machine;
use Modules\Xot\Filament\Resources\XotBaseResource;

class MachineResource extends XotBaseResource
{
    protected static ?string $model = Machine::class;

    /**
     * @return array<string, class-string<RelationManager>>
     */
    #[\Override]
    public static function getRelations(): array
    {
        return [];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListMachines::route('/'),
            'create' => CreateMachine::route('/create'),
            'view' => ViewMachine::route('/{record}'),
            'edit' => EditMachine::route('/{record}/edit'),
        ];
    }
}
