<?php

declare(strict_types=1);

namespace Modules\Processing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

/**
 * Natura di un'unità prodotta da un ordine di lavorazione.
 *
 * Etichetta, colore e icona: lang/{locale}/output_kind_enum.php.
 */
enum OutputKindEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case PRODUCT = 'product';
    case BY_PRODUCT = 'by_product';
    case SCRAP = 'scrap';
    case RESIDUE = 'residue';
}
