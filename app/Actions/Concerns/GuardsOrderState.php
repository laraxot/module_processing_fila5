<?php

declare(strict_types=1);

namespace Modules\Processing\Actions\Concerns;

use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Exceptions\InvalidOrderStateException;
use Modules\Processing\Models\ProcessingOrder;
use Webmozart\Assert\Assert;

/**
 * Lettura con lock dell'ordine e verifica dello stato, condivisa dalle Actions che lo modificano.
 */
trait GuardsOrderState
{
    /**
     * Rilegge l'ordine con `lockForUpdate`: va chiamato dentro una transazione.
     */
    protected function lockOrder(ProcessingOrder $order): ProcessingOrder
    {
        return ProcessingOrder::query()->lockForUpdate()->findOrFail($order->id);
    }

    /**
     * Regola SPEC: le transizioni consentite sono dichiarate dall'enum, le actions fanno Assert::true().
     */
    protected function assertTransition(ProcessingOrder $order, ProcessingOrderStatusEnum $to): void
    {
        // Messaggio costruito solo in caso di errore; Webmozart lo passa a sprintf(), quindi i '%' vanno raddoppiati.
        Assert::true(
            $order->status->canTransitionTo($to),
            static fn (): string => str_replace('%', '%%', InvalidOrderStateException::transition($order, $to)->getMessage()),
        );
    }

    protected function assertAcceptsMaterial(ProcessingOrder $order): void
    {
        if (! $order->status->acceptsMaterial()) {
            throw InvalidOrderStateException::notAcceptingMaterial($order);
        }
    }
}
