<?php

declare(strict_types=1);

namespace Modules\Processing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

/**
 * Ciclo di vita di un ordine di lavorazione.
 *
 * draft → in_progress → completed
 * draft → cancelled, in_progress → cancelled (solo se nessun input consumato: lo verifica l'action)
 *
 * Etichetta, colore e icona: lang/{locale}/processing_order_status_enum.php.
 */
enum ProcessingOrderStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case DRAFT = 'draft';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::DRAFT => in_array($to, [self::IN_PROGRESS, self::CANCELLED], true),
            self::IN_PROGRESS => in_array($to, [self::COMPLETED, self::CANCELLED], true),
            self::COMPLETED, self::CANCELLED => false,
        };
    }

    /** Stati in cui l'ordine accetta nuovi input e output. */
    public function acceptsMaterial(): bool
    {
        return $this === self::IN_PROGRESS;
    }

    public function isFinal(): bool
    {
        return $this === self::COMPLETED || $this === self::CANCELLED;
    }
}
