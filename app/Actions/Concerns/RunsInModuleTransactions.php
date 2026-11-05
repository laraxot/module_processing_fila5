<?php

declare(strict_types=1);

namespace Modules\Processing\Actions\Concerns;

use Closure;
use Illuminate\Support\Facades\DB;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Warehouse\Models\StockUnit;

/**
 * Transazione annidata sulle connessioni di Processing e Warehouse.
 *
 * I moduli possono stare su connessioni diverse: si apre una transazione per ciascuna connessione
 * distinta, una dentro l'altra. Se puntano allo stesso PDO (caso tipico e nei test) la seconda
 * diventa un savepoint; se sono database diversi l'atomicità è best-effort (nessun 2PC):
 * un errore prima del commit annulla entrambe, un errore DURANTE il commit esterno no.
 */
trait RunsInModuleTransactions
{
    /**
     * @template TReturn
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     */
    protected function inModuleTransaction(Closure $callback): mixed
    {
        $processing = (new ProcessingOrder)->getConnectionName() ?? DB::getDefaultConnection();
        $warehouse = (new StockUnit)->getConnectionName() ?? DB::getDefaultConnection();

        if ($processing === $warehouse) {
            return DB::connection($processing)->transaction($callback);
        }

        return DB::connection($processing)->transaction(
            static fn (): mixed => DB::connection($warehouse)->transaction($callback),
        );
    }
}
