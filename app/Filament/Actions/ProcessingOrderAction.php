<?php

declare(strict_types=1);

namespace Modules\Processing\Filament\Actions;

use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
<<<<<<< HEAD
=======
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
>>>>>>> laraxot/dev
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
<<<<<<< HEAD
=======
 *
 * Ogni bottone che cambia dati dichiara l'abilità della ProcessingOrderPolicy che lo governa
 * (`authorizeOrder()`): senza il permesso il bottone è nascosto e la chiamata Livewire diretta
 * viene rifiutata. Dentro la callback si ricontrolla con `ensureAllowed()`.
>>>>>>> laraxot/dev
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

<<<<<<< HEAD
    protected function resolveOrder(): ProcessingOrder
    {
=======
    /**
     * Le azioni native sul materiale ("Aggiungi input", "Registra output") sono attive?
     *
     * Punto di estensione `processing.material_actions_enabled` (default true): un modulo che
     * gestisce il materiale con regole proprie (in Roosko: lotto, QR, scarico di registro,
     * controllo dell'autorizzazione) lo spegne e offre le sue azioni al posto di queste.
     */
    public static function materialActionsEnabled(): bool
    {
        return config()->boolean('processing.material_actions_enabled', true);
    }

    /**
     * Ricontrollo dentro la callback delle azioni sul materiale: spente da config, non eseguono nulla.
     *
     * @throws AuthorizationException
     */
    protected function ensureMaterialActionsEnabled(): void
    {
        if (! static::materialActionsEnabled()) {
            throw new AuthorizationException(static::trans('messages.material_actions_disabled'));
        }
    }

    /** L'utente corrente ha l'abilità `$ability` della ProcessingOrderPolicy sull'ordine di questo bottone? */
    public function allows(string $ability): bool
    {
        $order = $this->findOrder();

        return $order instanceof ProcessingOrder && Gate::allows($ability, $order);
    }

    /**
     * Il bottone è visibile ed eseguibile solo con l'abilità `$ability` sull'ordine.
     */
    protected function authorizeOrder(string $ability): static
    {
        return $this->authorize(fn (): bool => $this->allows($ability));
    }

    /**
     * Ricontrollo dentro la callback (difesa in profondità rispetto alla visibilità del bottone).
     *
     * @throws AuthorizationException
     */
    protected function ensureAllowed(string $ability): void
    {
        Gate::authorize($ability, $this->resolveOrder());
    }

    protected function resolveOrder(): ProcessingOrder
    {
        $order = $this->findOrder();
        if ($order instanceof ProcessingOrder) {
            return $order;
        }

        throw new \LogicException(static::class.' requires a ProcessingOrder record or owner record.');
    }

    private function findOrder(): ?ProcessingOrder
    {
>>>>>>> laraxot/dev
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

<<<<<<< HEAD
        throw new \LogicException(static::class.' requires a ProcessingOrder record or owner record.');
=======
        return null;
>>>>>>> laraxot/dev
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
<<<<<<< HEAD
            $record = $this->getRecord();
            $livewire = $this->getLivewire();
            $order = $record instanceof ProcessingOrder
                ? $record
                : ($livewire instanceof RelationManager ? $livewire->getOwnerRecord() : null);
=======
            $order = $this->findOrder();
>>>>>>> laraxot/dev

            return $order instanceof ProcessingOrder && $condition($order);
        });
    }
}
