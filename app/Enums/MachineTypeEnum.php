<?php

declare(strict_types=1);

namespace Modules\Processing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

/**
 * Tipologia di macchina di lavorazione.
 *
 * Etichetta, colore e icona: lang/{locale}/machine_type_enum.php.
 */
enum MachineTypeEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case SHREDDER = 'shredder';
    case CRUSHER = 'crusher';
    case SORTER = 'sorter';
    case SCREEN = 'screen';
    case MAGNETIC_SEPARATOR = 'magnetic_separator';
    case BALER = 'baler';
    case PRESS = 'press';
    case OTHER = 'other';
}
