<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Actions;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Modules\Processing\Actions\RegisterProcessingOutputAction;
use Modules\Processing\Enums\OutputKindEnum;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Processing\Models\ProcessingOutput;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\Warehouse;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Actions\Cast\SafeNullableStringCastAction;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * "Registra output": crea la nuova unità in Warehouse (movimento production) e la lega all'ordine.
 */
class RegisterProcessingOutputHeaderAction extends ProcessingOrderAction
{
    public static function getDefaultName(): ?string
    {
        return 'register_output';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-plus-circle')
            ->color('success')
            ->schema([
                Select::make('kind')
                    ->options(OutputKindEnum::class)
                    ->default(OutputKindEnum::PRODUCT->value)
                    ->required(),
                Select::make('warehouse_id')
                    ->options(static fn (): array => self::codeOptions(Warehouse::query()->orderBy('code')->pluck('code', 'id')->all()))
                    ->required(),
                Select::make('container_type')
                    ->options(ContainerTypeEnum::class)
                    ->required(),
                TextInput::make('net_weight_kg')
                    ->numeric()
                    ->required()
                    ->minValue(0.001)
                    ->step(0.001)
                    ->suffix('kg'),
                TextInput::make('tare_weight_kg')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.001)
                    ->suffix('kg'),
                Select::make('location_id')
                    ->options(static fn (): array => self::codeOptions(Location::query()->orderBy('code')->pluck('code', 'id')->all()))
                    ->searchable(),
                Textarea::make('notes')->rows(2),
            ])
            ->isAvailableWhen(static fn (ProcessingOrder $order): bool => $order->status->acceptsMaterial())
            ->refreshOrderPage()
            ->action(function (array $data): void {
                $kindValue = $data['kind'] ?? null;
                $kind = $kindValue instanceof OutputKindEnum ? $kindValue : OutputKindEnum::from(SafeStringCastAction::cast($kindValue));
                $containerValue = $data['container_type'] ?? null;
                $container = $containerValue instanceof ContainerTypeEnum ? $containerValue : ContainerTypeEnum::from(SafeStringCastAction::cast($containerValue));

                $unitData = new StockUnitData(
                    warehouseId: SafeIntCastAction::cast($data['warehouse_id'] ?? null),
                    containerType: $container,
                    netWeightKg: is_numeric($data['net_weight_kg'] ?? null) ? (float) $data['net_weight_kg'] : 0.0,
                    tareWeightKg: is_numeric($data['tare_weight_kg'] ?? null) ? (float) $data['tare_weight_kg'] : null,
                    locationId: is_numeric($data['location_id'] ?? null) ? (int) $data['location_id'] : null,
                    notes: SafeNullableStringCastAction::cast($data['notes'] ?? null),
                );

                $this->runDomain(
                    fn (): ProcessingOutput => app(RegisterProcessingOutputAction::class)->execute($this->resolveOrder(), $unitData, $kind),
                    keepModalOpen: true,
                );
            });
    }

    /**
     * @param  array<array-key, mixed>  $rows
     * @return array<int, string>
     */
    private static function codeOptions(array $rows): array
    {
        $options = [];
        foreach ($rows as $id => $code) {
            $options[(int) $id] = is_scalar($code) ? (string) $code : '';
        }

        return $options;
    }
}
