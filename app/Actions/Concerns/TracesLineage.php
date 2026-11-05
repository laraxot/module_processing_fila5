<?php

declare(strict_types=1);

namespace Modules\Processing\Actions\Concerns;

use Illuminate\Support\Collection;
use Modules\Processing\Datas\LineageNodeData;
use Modules\Processing\Models\LineageLink;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Webmozart\Assert\Assert;

/**
 * Visita in ampiezza (BFS) del grafo `lineage_links`, un livello per query.
 *
 * Ogni nodo restituito è un cammino: se un'unità è raggiungibile da due cammini (diamante:
 * due input dello stesso ordine che derivano dallo stesso lotto) compare due volte, ciascuna con la
 * quota del proprio cammino. Così l'albero è fedele e la somma delle quote per `stockUnitId` è esatta.
 *
 * Protezione dai cicli: un cammino non riattraversa un'unità già presente tra i suoi antenati.
 * Il grafo è aciclico per costruzione (un'unità prodotta non può essere input del proprio ordine), ma
 * dati importati o rettifiche manuali possono introdurre cicli: la visita termina comunque.
 * Limiti: `$maxDepth` livelli e MAX_NODES nodi complessivi.
 */
trait TracesLineage
{
    /** Tetto di sicurezza contro grafi patologici (fan-in elevato su molti livelli). */
    private const int MAX_NODES = 5000;

    /**
     * @return Collection<int, LineageNodeData>
     */
    protected function trace(StockUnit $unit, int $maxDepth, bool $upstream): Collection
    {
        Assert::greaterThanEq($maxDepth, 1);

        $rootId = SafeIntCastAction::cast($unit->getKey());
        $fromColumn = $upstream ? 'child_stock_unit_id' : 'parent_stock_unit_id';

        /** @var list<array{id: int, index: int|null, share: float, kg: float|null, ancestors: array<int, true>}> $frontier */
        $frontier = [['id' => $rootId, 'index' => null, 'share' => 1.0, 'kg' => null, 'ancestors' => [$rootId => true]]];

        /** @var list<array{stockUnitId: int, depth: int, share: float, kg: float, orderId: int, via: int, parent: int|null}> $found */
        $found = [];

        for ($depth = 1; $depth <= $maxDepth && $frontier !== [] && count($found) < self::MAX_NODES; $depth++) {
            $frontierIds = array_values(array_unique(array_column($frontier, 'id')));

            /** @var array<int, list<LineageLink>> $linksByFrom */
            $linksByFrom = [];
            foreach (LineageLink::query()->whereIn($fromColumn, $frontierIds)->orderBy('id')->get() as $link) {
                $from = $upstream ? $link->child_stock_unit_id : $link->parent_stock_unit_id;
                $linksByFrom[$from][] = $link;
            }

            $next = [];
            foreach ($frontier as $path) {
                foreach ($linksByFrom[$path['id']] ?? [] as $link) {
                    $to = $upstream ? $link->parent_stock_unit_id : $link->child_stock_unit_id;
                    if (isset($path['ancestors'][$to])) {
                        continue;
                    }

                    $share = (float) $link->share;
                    $cumulativeShare = $path['share'] * $share;
                    // Al primo livello i kg sono quelli dell'arco; poi si scalano per la quota del nuovo arco
                    // (a monte) o per la quota cumulata del cammino (a valle). Vedi LineageNodeData.
                    $attributedKg = match (true) {
                        $path['kg'] === null => (float) $link->attributed_kg,
                        $upstream => $path['kg'] * $share,
                        default => (float) $link->attributed_kg * $path['share'],
                    };

                    $found[] = [
                        'stockUnitId' => $to,
                        'depth' => $depth,
                        'share' => $cumulativeShare,
                        'kg' => $attributedKg,
                        'orderId' => $link->processing_order_id,
                        'via' => $path['id'],
                        'parent' => $path['index'],
                    ];
                    $next[] = [
                        'id' => $to,
                        'index' => array_key_last($found),
                        'share' => $cumulativeShare,
                        'kg' => $attributedKg,
                        'ancestors' => $path['ancestors'] + [$to => true],
                    ];

                    if (count($found) >= self::MAX_NODES) {
                        break 2;
                    }
                }
            }
            $frontier = $next;
        }

        /** @var array<int, string> $codes */
        $codes = StockUnit::query()
            ->whereKey(array_values(array_unique(array_column($found, 'stockUnitId'))))
            ->pluck('code', 'id')
            ->map(static fn (mixed $code): string => is_string($code) ? $code : '')
            ->all();

        return collect($found)->map(static fn (array $node): LineageNodeData => new LineageNodeData(
            stockUnitId: $node['stockUnitId'],
            code: $codes[$node['stockUnitId']] ?? '#'.$node['stockUnitId'],
            depth: $node['depth'],
            cumulativeShare: round($node['share'], 6),
            attributedKg: round($node['kg'], 3),
            processingOrderId: $node['orderId'],
            viaStockUnitId: $node['via'],
            parentIndex: $node['parent'],
        ))->values();
    }
}
