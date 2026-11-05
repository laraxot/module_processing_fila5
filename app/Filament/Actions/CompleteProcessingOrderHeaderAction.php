<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Actions;

use Filament\Forms\Components\Toggle;
use Modules\Processing\Actions\CompleteProcessingOrderAction;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Xot\Actions\Cast\SafeBooleanCastAction;

/**
 * Chiude l'ordine: bilancio di massa e genealogia.
 *
 * Se la perdita supera la soglia l'Action di dominio rifiuta; il modale resta aperto
 * e l'operatore può confermare con "forza chiusura".
 */
class CompleteProcessingOrderHeaderAction extends ProcessingOrderAction
{
    public static function getDefaultName(): ?string
    {
        return 'complete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-check-circle')
            ->color('success')
            ->schema([
                Toggle::make('force')->default(false),
            ])
            ->isAvailableWhen(static fn (ProcessingOrder $order): bool => $order->status->canTransitionTo(ProcessingOrderStatusEnum::COMPLETED))
            ->redirectToOrder()
            ->action(function (array $data): void {
                $force = SafeBooleanCastAction::cast($data['force'] ?? false);
                $this->runDomain(
                    fn (): ProcessingOrder => app(CompleteProcessingOrderAction::class)->execute($this->resolveOrder(), $force),
                    keepModalOpen: true,
                );
            });
    }
}
