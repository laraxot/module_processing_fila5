<?php

declare(strict_types=1);

namespace Modules\Processing\Actions;

use Illuminate\Support\Collection;
use Modules\Processing\Actions\Concerns\TracesLineage;
use Modules\Processing\Datas\LineageNodeData;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;

/**
 * Tracciabilità a valle: in quali unità (e in che quota) è finita l'unità data.
 *
 * `cumulativeShare` = frazione della massa del nodo che proviene dall'unità data;
 * `attributedKg` = kg del nodo attribuiti all'unità data.
 */
class TraceDownstreamAction
{
    use QueueableAction;
    use TracesLineage;

    /**
     * @return Collection<int, LineageNodeData>
     */
    public function execute(StockUnit $unit, int $maxDepth = 20): Collection
    {
        return $this->trace($unit, $maxDepth, upstream: false);
    }
}
