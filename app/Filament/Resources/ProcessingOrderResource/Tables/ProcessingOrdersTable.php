<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\ProcessingOrderResource\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

class ProcessingOrdersTable extends XotBaseResourceTable
{
    /**
     * @var class-string<ProcessingOrder>
     */
    protected static string $model = ProcessingOrder::class;

    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'code' => TextColumn::make('code')->searchable()->sortable(),
            'status' => TextColumn::make('status')->badge()->sortable(),
            'machine.name' => TextColumn::make('machine.name')->searchable()->sortable(),
            'operation_code' => TextColumn::make('operation_code')->searchable()->toggleable(),
            'planned_at' => TextColumn::make('planned_at')->dateTime()->sortable()->toggleable(),
            'started_at' => TextColumn::make('started_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            'completed_at' => TextColumn::make('completed_at')->dateTime()->sortable()->toggleable(),
            'input_kg' => TextColumn::make('input_kg')->numeric(3)->sortable()->toggleable(),
            'output_kg' => TextColumn::make('output_kg')->numeric(3)->sortable()->toggleable(),
            'loss_pct' => TextColumn::make('loss_pct')->numeric(2)->suffix(' %')->sortable()->toggleable(),
        ];
    }

    public function getTableFilters(): array
    {
        return [
            'status' => SelectFilter::make('status')->options(ProcessingOrderStatusEnum::class),
            'machine_id' => SelectFilter::make('machine_id')->relationship('machine', 'name'),
        ];
    }

    public function getTableActions(): array
    {
        return [
            'view' => ViewAction::make(),
            'edit' => EditAction::make()
                ->visible(static fn (ProcessingOrder $record): bool => ! $record->status->isFinal()),
        ];
    }

    /** Nessuna eliminazione massiva: gli ordini sono documenti di tracciabilità. */
    public function getTableBulkActions(): array
    {
        return [];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'code';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}
