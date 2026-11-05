<?php

declare(strict_types=1);

namespace Modules\Processing\Datas;

use Spatie\LaravelData\Data;

/**
 * Nodo dell'albero di tracciabilità restituito da TraceUpstreamAction / TraceDownstreamAction.
 *
 * - `depth`: distanza (in passi di lavorazione) dall'unità di partenza, 1 = parente diretto.
 * - `cumulativeShare`: prodotto delle `share` lungo il cammino.
 *   A monte: frazione della massa dell'unità di partenza che proviene da questo nodo.
 *   A valle: frazione della massa di questo nodo che proviene dall'unità di partenza.
 * - `attributedKg`: kg corrispondenti alla quota cumulata
 *   (a monte: kg dell'unità di partenza attribuiti al nodo; a valle: kg del nodo attribuiti all'unità di partenza).
 * - `processingOrderId`: ordine di lavorazione dell'arco che collega il nodo al precedente.
 * - `viaStockUnitId`: unità da cui il nodo è stato raggiunto.
 * - `parentIndex`: indice, nella stessa collection, del nodo da cui è stato raggiunto (null = figlio diretto
 *   dell'unità di partenza). Serve a ricostruire l'albero senza ambiguità quando un'unità compare su più cammini.
 */
final class LineageNodeData extends Data
{
    public function __construct(
        public readonly int $stockUnitId,
        public readonly string $code,
        public readonly int $depth,
        public readonly float $cumulativeShare,
        public readonly float $attributedKg,
        public readonly int $processingOrderId,
        public readonly ?int $viaStockUnitId = null,
        public readonly ?int $parentIndex = null,
    ) {}
}
