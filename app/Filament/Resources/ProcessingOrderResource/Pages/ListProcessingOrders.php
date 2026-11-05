<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\ProcessingOrderResource\Pages;

use Modules\Processing\Filament\Resources\ProcessingOrderResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseListRecords;

class ListProcessingOrders extends XotBaseListRecords
{
    protected static string $resource = ProcessingOrderResource::class;
}
