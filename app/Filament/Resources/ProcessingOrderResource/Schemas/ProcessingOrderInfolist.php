<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\ProcessingOrderResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

class ProcessingOrderInfolist extends XotBaseResourceInfolist
{
    /**
     * @return array<string, Component>
     */
    public function getInfolistSchema(): array
    {
        return [
            'order' => Section::make('order')
                ->columns(3)
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('code'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('machine.name'),
                    TextEntry::make('operation_code'),
                    TextEntry::make('planned_at')->dateTime(),
                    TextEntry::make('started_at')->dateTime(),
                    TextEntry::make('completed_at')->dateTime(),
                    TextEntry::make('cancelled_at')->dateTime(),
                    TextEntry::make('cancel_reason'),
                    TextEntry::make('notes')->columnSpanFull(),
                ]),
            'mass_balance' => Section::make('mass_balance')
                ->columns(4)
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('input_kg')->numeric(3)->suffix(' kg'),
                    TextEntry::make('output_kg')->numeric(3)->suffix(' kg'),
                    TextEntry::make('loss_kg')->numeric(3)->suffix(' kg'),
                    TextEntry::make('loss_pct')->numeric(2)->suffix(' %'),
                ]),
        ];
    }
}
