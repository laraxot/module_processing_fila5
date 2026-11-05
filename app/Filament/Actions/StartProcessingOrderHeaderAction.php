<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Actions;

use Modules\Processing\Actions\StartProcessingOrderAction;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Models\ProcessingOrder;

/**
 * Avvia l'ordine (draft → in_progress).
 */
class StartProcessingOrderHeaderAction extends ProcessingOrderAction
{
    public static function getDefaultName(): ?string
    {
        return 'start';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-play')
            ->color('primary')
            ->requiresConfirmation()
            ->isAvailableWhen(static fn (ProcessingOrder $order): bool => $order->status->canTransitionTo(ProcessingOrderStatusEnum::IN_PROGRESS))
            ->redirectToOrder()
            ->action(function (): void {
                $this->runDomain(fn (): ProcessingOrder => app(StartProcessingOrderAction::class)->execute($this->resolveOrder()));
            });
    }
}
