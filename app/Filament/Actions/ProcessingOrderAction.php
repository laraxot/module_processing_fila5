<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Actions;

use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Livewire\Component;
use Modules\Processing\Filament\Resources\ProcessingOrderResource;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Xot\Filament\Actions\XotBaseAction;
use Webmozart\Assert\InvalidArgumentException as AssertionFailed;

/**
 * Base dei bottoni Filament che operano su un ProcessingOrder.
 *
 * Nessuna logica di dominio: raccolgono l'input, chiamano l'Action di dominio, notificano.
 * Testi da `lang/{locale}/processing_order.php` sotto `actions.{nome}.*`.
 * Funzionano sia come header action della pagina View (record = ordine) sia come
 * header action di un RelationManager dell'ordine (ordine = owner record).
 */
abstract class ProcessingOrderAction extends XotBaseAction
{
    public static function trans(string $key): string
    {
        return ProcessingOrderResource::trans($key);
    }

    protected function actionTrans(string $suffix): string
    {
        return static::trans('actions.'.(string) $this->getName().'.'.$suffix);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (): string => $this->actionTrans('label'));
        $this->tooltip(fn (): string => $this->actionTrans('tooltip'));
        $this->modalHeading(fn (): string => $this->actionTrans('modal_heading'));
        $this->modalDescription(fn (): string => $this->actionTrans('modal_description'));
        $this->successNotificationTitle(fn (): string => $this->actionTrans('success'));
    }

    protected function resolveOrder(): ProcessingOrder
    {
        $record = $this->getRecord();
        if ($record instanceof ProcessingOrder) {
            return $record;
        }

        $livewire = $this->getLivewire();
        if ($livewire instanceof RelationManager) {
            $owner = $livewire->getOwnerRecord();
            if ($owner instanceof ProcessingOrder) {
                return $owner;
            }
        }

        throw new \LogicException(static::class.' requires a ProcessingOrder record or owner record.');
    }

    /**
     * Esegue la chiamata di dominio; un rifiuto di dominio diventa una notifica e ferma l'azione.
     *
     * @param  \Closure(): mixed  $callback
     */
    protected function runDomain(\Closure $callback, bool $keepModalOpen = false): void
    {
        try {
            $callback();
        } catch (\DomainException|AssertionFailed $e) {
            Notification::make()
                ->danger()
                ->title($this->actionTrans('error'))
                ->body($e->getMessage())
                ->send();

            $keepModalOpen ? $this->halt() : $this->cancel();
        }
    }

    /**
     * Dopo un cambio di stato si ricarica la pagina dell'ordine: infolist, header actions e RM coerenti.
     */
    protected function redirectToOrder(): static
    {
        return $this->successRedirectUrl(fn (): string => ProcessingOrderResource::getUrl('view', [
            'record' => $this->resolveOrder(),
        ]));
    }

    /**
     * Dopo un input/output aggiunto da un RelationManager la pagina dell'ordine (header actions, infolist)
     * si aggiorna: BasePage di Filament ascolta l'evento `refresh-page`.
     */
    protected function refreshOrderPage(): static
    {
        return $this->after(function (): void {
            $livewire = $this->getLivewire();
            if ($livewire instanceof Component) {
                $livewire->dispatch('refresh-page');
            }
        });
    }

    /**
     * @param  \Closure(ProcessingOrder): bool  $condition
     */
    protected function isAvailableWhen(\Closure $condition): static
    {
        return $this->visible(function () use ($condition): bool {
            $record = $this->getRecord();
            $livewire = $this->getLivewire();
            $order = $record instanceof ProcessingOrder
                ? $record
                : ($livewire instanceof RelationManager ? $livewire->getOwnerRecord() : null);

            return $order instanceof ProcessingOrder && $condition($order);
        });
    }
}
