<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\ProcessingOrderResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Modules\Processing\Filament\Actions\CancelProcessingOrderHeaderAction;
use Modules\Processing\Filament\Actions\CompleteProcessingOrderHeaderAction;
use Modules\Processing\Filament\Actions\StartProcessingOrderHeaderAction;
use Modules\Processing\Filament\Resources\ProcessingOrderResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseViewRecord;

class ViewProcessingOrder extends XotBaseViewRecord
{
    protected static string $resource = ProcessingOrderResource::class;

    /**
     * @return array<string, Action|ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            'start' => StartProcessingOrderHeaderAction::make(),
            'complete' => CompleteProcessingOrderHeaderAction::make(),
            'cancel' => CancelProcessingOrderHeaderAction::make(),
        ];
    }
}
