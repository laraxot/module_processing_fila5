<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\MachineResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Modules\Processing\Enums\MachineTypeEnum;
use Modules\Warehouse\Models\Location;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

class MachineForm extends XotBaseResourceForm
{
    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        return [
            'code' => TextInput::make('code')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true),
            'name' => TextInput::make('name')
                ->required()
                ->maxLength(255),
            'type' => Select::make('type')
                ->options(MachineTypeEnum::class)
                ->required(),
            // Location di Warehouse: connessione diversa, niente relationship() di Eloquent sul form.
            'location_ref_id' => Select::make('location_ref_id')
                ->options(static fn (): array => Location::query()
                    ->orderBy('code')
                    ->pluck('code', 'id')
                    ->map(static fn (mixed $code): string => is_scalar($code) ? (string) $code : '')
                    ->all())
                ->searchable(),
            'capacity_kg_h' => TextInput::make('capacity_kg_h')
                ->numeric()
                ->minValue(0)
                ->step(0.001)
                ->suffix('kg/h'),
            'is_active' => Toggle::make('is_active')
                ->default(true),
        ];
    }
}
