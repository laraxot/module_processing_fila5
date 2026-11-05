<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Resources\ProcessingOrderResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Models\Machine;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

/**
 * Dati di pianificazione dell'ordine. Codice, stato e bilancio sono scritti solo dalle Actions.
 * La macchina si cambia solo in bozza; a ordine chiuso il form è in sola lettura.
 */
class ProcessingOrderForm extends XotBaseResourceForm
{
    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        return [
            'machine_id' => Select::make('machine_id')
                ->relationship(
                    'machine',
                    'name',
                    static fn (Builder $query): Builder => $query->where('is_active', true),
                )
                ->getOptionLabelFromRecordUsing(static fn (Machine $record): string => $record->code.' — '.$record->name)
                ->searchable()
                ->preload()
                ->required()
                ->disabled(static fn (?ProcessingOrder $record): bool => $record !== null && $record->status !== ProcessingOrderStatusEnum::DRAFT),
            'operation_code' => TextInput::make('operation_code')
                ->maxLength(20)
                ->disabled(self::isClosed(...)),
            'planned_at' => DateTimePicker::make('planned_at')
                ->disabled(self::isClosed(...)),
            'notes' => Textarea::make('notes')
                ->rows(3)
                ->columnSpanFull()
                ->disabled(self::isClosed(...)),
        ];
    }

    private static function isClosed(?ProcessingOrder $record): bool
    {
        return $record !== null && $record->status->isFinal();
    }
}
