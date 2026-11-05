<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\MachineResource\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Modules\Processing\Enums\MachineTypeEnum;
use Modules\Processing\Models\Machine;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

class MachinesTable extends XotBaseResourceTable
{
    /**
     * @var class-string<Machine>
     */
    protected static string $model = Machine::class;

    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'code' => TextColumn::make('code')->searchable()->sortable(),
            'name' => TextColumn::make('name')->searchable()->sortable(),
            'type' => TextColumn::make('type')->badge()->sortable(),
            'capacity_kg_h' => TextColumn::make('capacity_kg_h')->numeric(3)->sortable()->toggleable(),
            'is_active' => IconColumn::make('is_active')->boolean()->sortable(),
            'updated_at' => TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public function getTableFilters(): array
    {
        return [
            'type' => SelectFilter::make('type')->options(MachineTypeEnum::class),
            'is_active' => TernaryFilter::make('is_active'),
        ];
    }

    public function getTableActions(): array
    {
        return [
            'view' => ViewAction::make(),
            'edit' => EditAction::make(),
        ];
    }

    public function getTableBulkActions(): array
    {
        return [
            'delete' => DeleteBulkAction::make(),
        ];
    }
}
