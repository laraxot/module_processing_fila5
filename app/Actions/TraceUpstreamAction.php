<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Illuminate\Support\Collection;
use Modules\Processing\Actions\Concerns\TracesLineage;
use Modules\Processing\Datas\LineageNodeData;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;

/**
 * Tracciabilità a monte: da quali unità (e in che quota) deriva l'unità data.
 *
 * `cumulativeShare` = frazione della massa dell'unità data che proviene dal nodo;
 * `attributedKg` = kg dell'unità data attribuiti al nodo.
 */
class TraceUpstreamAction
{
    use QueueableAction;
    use TracesLineage;

    /**
     * @return Collection<int, LineageNodeData>
     */
    public function execute(StockUnit $unit, int $maxDepth = 20): Collection
    {
        return $this->trace($unit, $maxDepth, upstream: true);
    }
}
