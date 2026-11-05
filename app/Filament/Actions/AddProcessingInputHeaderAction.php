<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Actions;

use Filament\Forms\Components\TextInput;
use Modules\Processing\Actions\AddProcessingInputAction;
use Modules\Processing\Actions\ResolveStockUnitByCodeAction;
use Modules\Processing\Exceptions\InvalidMaterialException;
use Modules\Processing\Models\ProcessingInput;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * "Aggiungi input": l'operatore digita o scansiona il codice dell'unità (lettore barcode/RFID in
 * keyboard wedge: il campo ha il fuoco e il lettore "digita" il codice seguito da Invio).
 * Kg vuoto = intera unità.
 *
 * Azione nativa sul materiale: consuma l'unità in Warehouse senza altre regole di dominio
 * (nessun lotto, nessuno scarico di registro). Si spegne con `processing.material_actions_enabled`
 * quando un altro modulo (Roosko) gestisce gli input con le proprie regole.
 */
class AddProcessingInputHeaderAction extends ProcessingOrderAction
{
    public static function getDefaultName(): ?string
    {
        return 'add_input';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-qr-code')
            ->color('primary')
            ->schema([
                TextInput::make('stock_unit_code')
                    ->required()
                    ->autofocus()
                    ->autocomplete(false)
                    ->maxLength(255),
                TextInput::make('quantity_kg')
                    ->numeric()
                    ->minValue(0.001)
                    ->step(0.001)
                    ->suffix('kg'),
            ])
            ->authorizeOrder('addInput')
            ->isAvailableWhen(static fn (ProcessingOrder $order): bool => static::materialActionsEnabled() && $order->status->acceptsMaterial())
            ->refreshOrderPage()
            ->action(function (array $data): void {
                $this->ensureMaterialActionsEnabled();
                $this->ensureAllowed('addInput');
                $code = SafeStringCastAction::cast($data['stock_unit_code'] ?? '');
                $kg = isset($data['quantity_kg']) && is_numeric($data['quantity_kg']) ? (float) $data['quantity_kg'] : null;

                $this->runDomain(function () use ($code, $kg): ProcessingInput {
                    $unit = app(ResolveStockUnitByCodeAction::class)->execute($code);
                    if ($unit === null) {
                        throw InvalidMaterialException::unitNotFound($code);
                    }

                    return app(AddProcessingInputAction::class)->execute($this->resolveOrder(), $unit, $kg);
                }, keepModalOpen: true);
            });
    }
}
