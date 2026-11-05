<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources;

use Filament\Resources\RelationManagers\RelationManager;
use Modules\Processing\Filament\Resources\ProcessingOrderResource\Pages\CreateProcessingOrder;
use Modules\Processing\Filament\Resources\ProcessingOrderResource\Pages\EditProcessingOrder;
use Modules\Processing\Filament\Resources\ProcessingOrderResource\Pages\ListProcessingOrders;
use Modules\Processing\Filament\Resources\ProcessingOrderResource\Pages\ViewProcessingOrder;
use Modules\Processing\Filament\Resources\ProcessingOrderResource\RelationManagers\InputsRelationManager;
use Modules\Processing\Filament\Resources\ProcessingOrderResource\RelationManagers\OutputsRelationManager;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Xot\Filament\Resources\XotBaseResource;

class ProcessingOrderResource extends XotBaseResource
{
    protected static ?string $model = ProcessingOrder::class;

    /**
     * @return array<string, class-string<RelationManager>>
     */
    #[\Override]
    public static function getRelations(): array
    {
        return [
            'inputs' => InputsRelationManager::class,
            'outputs' => OutputsRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListProcessingOrders::route('/'),
            'create' => CreateProcessingOrder::route('/create'),
            'view' => ViewProcessingOrder::route('/{record}'),
            'edit' => EditProcessingOrder::route('/{record}/edit'),
        ];
    }
}
