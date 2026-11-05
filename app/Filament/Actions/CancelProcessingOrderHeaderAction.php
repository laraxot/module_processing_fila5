<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Actions;

use Filament\Forms\Components\Textarea;
use Modules\Processing\Actions\CancelProcessingOrderAction;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Annulla l'ordine (solo senza materiale movimentato), con motivo obbligatorio.
 */
class CancelProcessingOrderHeaderAction extends ProcessingOrderAction
{
    public static function getDefaultName(): ?string
    {
        return 'cancel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-x-circle')
            ->color('danger')
            ->schema([
                Textarea::make('reason')->required()->maxLength(1000)->rows(3),
            ])
<<<<<<< HEAD
=======
            ->authorizeOrder('cancel')
>>>>>>> laraxot/dev
            ->isAvailableWhen(static fn (ProcessingOrder $order): bool => $order->status->canTransitionTo(ProcessingOrderStatusEnum::CANCELLED)
                && $order->inputs()->doesntExist()
                && $order->outputs()->doesntExist())
            ->redirectToOrder()
            ->action(function (array $data): void {
<<<<<<< HEAD
=======
                $this->ensureAllowed('cancel');
>>>>>>> laraxot/dev
                $reason = SafeStringCastAction::cast($data['reason'] ?? '');
                $this->runDomain(
                    fn (): ProcessingOrder => app(CancelProcessingOrderAction::class)->execute($this->resolveOrder(), $reason),
                    keepModalOpen: true,
                );
            });
    }
}
