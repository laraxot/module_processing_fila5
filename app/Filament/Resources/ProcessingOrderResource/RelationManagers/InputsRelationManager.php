<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\ProcessingOrderResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Modules\Processing\Filament\Actions\AddProcessingInputHeaderAction;
use Modules\Xot\Filament\Resources\RelationManagers\XotBaseRelationManager;

/**
 * Input dell'ordine (unità consumate). Sola lettura: si aggiungono con "aggiungi input"
 * (codice digitato o scansionato), non si modificano né si eliminano.
 */
class InputsRelationManager extends XotBaseRelationManager
{
    protected static string $relationship = 'inputs';

    protected static ?string $recordTitleAttribute = 'stock_unit_id';

    /**
     * L'azione "aggiungi input" resta disponibile anche quando il RM è mostrato nella pagina View.
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
            'quantity_kg' => TextColumn::make('quantity_kg')->numeric(3)->suffix(' kg')->sortable(),
            'consumed_at' => TextColumn::make('consumed_at')->dateTime()->sortable(),
        ];
    }

    /**
     * @return array<string, Action>
     */
    #[\Override]
    public function getTableHeaderActions(): array
    {
        return [
            'add_input' => AddProcessingInputHeaderAction::make(),
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
