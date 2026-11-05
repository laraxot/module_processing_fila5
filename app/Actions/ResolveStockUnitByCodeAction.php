<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Illuminate\Support\Str;
use Modules\Labeling\Actions\ResolveIdentifiableAction;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;

/**
 * Trova l'unità di magazzino da un codice digitato o scansionato (lettore in keyboard wedge).
 *
 * 1. codice unità esatto (`SU-2026-000123`), anche estratto da un URL di etichetta `.../l/{code}`;
 * 2. altrimenti delega a Labeling (`ResolveIdentifiableAction`: QR, barcode, RFID) se il modulo è presente
 *    (dipendenza transitiva via Warehouse) e il modello risolto è una StockUnit.
 */
class ResolveStockUnitByCodeAction
{
    use QueueableAction;

    public function execute(string $scanned): ?StockUnit
    {
        $value = trim($scanned);
        if ($value === '') {
            return null;
        }

        $code = Str::contains($value, '/l/') ? Str::afterLast($value, '/l/') : $value;
        $code = Str::upper(trim(Str::before($code, '?'), '/ '));

        $unit = StockUnit::query()->where('code', $code)->first();
        if ($unit instanceof StockUnit) {
            return $unit;
        }

        if (! class_exists(ResolveIdentifiableAction::class)) {
            return null;
        }

        $resolved = app(ResolveIdentifiableAction::class)->execute($value);

        return $resolved instanceof StockUnit ? $resolved : null;
    }
}
