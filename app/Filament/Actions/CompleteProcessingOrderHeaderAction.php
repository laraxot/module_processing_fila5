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
 * e chi ha l'abilità `forceComplete` (permesso `processing_order.forceComplete`, di norma il
 * responsabile) può confermare con "forza chiusura". Senza l'abilità il toggle non compare e una
 * richiesta con `force = true` viene rifiutata.
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
                Toggle::make('force')
                    ->default(false)
                    ->visible(fn (): bool => $this->allows('forceComplete')),
            ])
            ->authorizeOrder('complete')
            ->isAvailableWhen(static fn (ProcessingOrder $order): bool => $order->status->canTransitionTo(ProcessingOrderStatusEnum::COMPLETED))
            ->redirectToOrder()
            ->action(function (array $data): void {
                $this->ensureAllowed('complete');
                $force = SafeBooleanCastAction::cast($data['force'] ?? false);
                if ($force) {
                    $this->ensureAllowed('forceComplete');
                }
                $this->runDomain(
                    fn (): ProcessingOrder => app(CompleteProcessingOrderAction::class)->execute($this->resolveOrder(), $force),
                    keepModalOpen: true,
                );
            });
    }
}
