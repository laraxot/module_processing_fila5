<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\MachineResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

class MachineInfolist extends XotBaseResourceInfolist
{
    /**
     * @return array<string, Component>
     */
    public function getInfolistSchema(): array
    {
        return [
            'code' => TextEntry::make('code'),
            'name' => TextEntry::make('name'),
            'type' => TextEntry::make('type')->badge(),
            'location.code' => TextEntry::make('location.code'),
            'capacity_kg_h' => TextEntry::make('capacity_kg_h')->numeric(3)->suffix(' kg/h'),
            'is_active' => IconEntry::make('is_active')->boolean(),
            'created_at' => TextEntry::make('created_at')->dateTime(),
            'updated_at' => TextEntry::make('updated_at')->dateTime(),
        ];
    }
}
