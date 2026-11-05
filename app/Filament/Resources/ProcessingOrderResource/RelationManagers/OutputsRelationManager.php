<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\ProcessingOrderResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Modules\Processing\Filament\Actions\RegisterProcessingOutputHeaderAction;
use Modules\Xot\Filament\Resources\RelationManagers\XotBaseRelationManager;

/**
 * Output dell'ordine (unità prodotte). Sola lettura: si registrano con "registra output"
 * (nuova unità in Warehouse), non si modificano né si eliminano.
 */
class OutputsRelationManager extends XotBaseRelationManager
{
    protected static string $relationship = 'outputs';

    protected static ?string $recordTitleAttribute = 'stock_unit_id';

    /**
     * L'azione "registra output" resta disponibile anche quando il RM è mostrato nella pagina View.
     */
    public function isReadOnly(): bool
    {
        return false;
    }

    /**
     * @return array<string, Column>
     */
    #[\Override]
    public function getTableColumns(): array
    {
        return [
            'stockUnit.code' => TextColumn::make('stockUnit.code'), // relazione cross-connessione: niente search/sort via join
            'kind' => TextColumn::make('kind')->badge()->sortable(),
            'quantity_kg' => TextColumn::make('quantity_kg')->numeric(3)->suffix(' kg')->sortable(),
            'produced_at' => TextColumn::make('produced_at')->dateTime()->sortable(),
        ];
    }

    /**
     * @return array<string, Action>
     */
    #[\Override]
    public function getTableHeaderActions(): array
    {
        return [
            'register_output' => RegisterProcessingOutputHeaderAction::make(),
        ];
    }

    /**
     * @return array<string, Action>
     */
    #[\Override]
    public function getTableActions(): array
    {
        return [];
    }

    /**
     * @return array<string, BulkAction>
     */
    #[\Override]
    public function getTableBulkActions(): array
    {
        return [];
    }
}
