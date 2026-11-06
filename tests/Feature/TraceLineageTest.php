<?php

declare(strict_types=1);

namespace Modules\Processing\Tests\Feature;

use Modules\Processing\Actions\TraceDownstreamAction;
use Modules\Processing\Actions\TraceUpstreamAction;
use Modules\Processing\Datas\LineageNodeData;
use Modules\Processing\Models\LineageLink;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Processing\Tests\TestCase;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * Grafo a 3 livelli (kg):
 *
 *   ordine 1:  A(600) + B(400)          → C(900)            share A 0.6, B 0.4
 *   ordine 2:  C(900) + D(300)          → E(800) + F(300)   share C 0.75, D 0.25
 *   ordine 3:  E(800)                   → G(760)            share E 1
 */
/**
 * @return array{units: array<string, StockUnit>, orders: list<ProcessingOrder>}
 */
function threeLevelLineage(): array
{
    $units = [];
    foreach (['A' => 600, 'B' => 400, 'C' => 900, 'D' => 300, 'E' => 800, 'F' => 300, 'G' => 760] as $name => $kg) {
        $unit = StockUnit::factory()->create(['net_weight_kg' => $kg]);
        Assert::assertInstanceOf(StockUnit::class, $unit);
        $units[$name] = $unit;
    }

    $orders = [];
    for ($i = 0; $i < 3; $i++) {
        $order = ProcessingOrder::factory()->create();
        Assert::assertInstanceOf(ProcessingOrder::class, $order);
        $orders[] = $order;
    }

    $edges = [
        [0, 'A', 'C', 0.6, 540.0], [0, 'B', 'C', 0.4, 360.0],
        [1, 'C', 'E', 0.75, 600.0], [1, 'D', 'E', 0.25, 200.0],
        [1, 'C', 'F', 0.75, 225.0], [1, 'D', 'F', 0.25, 75.0],
        [2, 'E', 'G', 1.0, 760.0],
    ];
    foreach ($edges as [$order, $parent, $child, $share, $kg]) {
        LineageLink::factory()
            ->between($units[$parent], $units[$child], $share, $kg)
            ->create(['processing_order_id' => $orders[$order]->id]);
    }

    return ['units' => $units, 'orders' => $orders];
}

/**
 * @param  iterable<LineageNodeData>  $nodes
 * @return array<string, LineageNodeData>
 */
function nodesByCode(iterable $nodes): array
{
    $map = [];
    foreach ($nodes as $node) {
        $map[$node->code] = $node;
    }

    return $map;
}

describe('TraceUpstreamAction', function (): void {
    test('risale tre livelli con quota cumulata e kg attribuiti', function (): void {
        ['units' => $u, 'orders' => $orders] = threeLevelLineage();
        $nodes = app(TraceUpstreamAction::class)->execute($u['G']);
        $byCode = nodesByCode($nodes);

        Assert::assertCount(5, $nodes);
        Assert::assertSame(
            [$u['E']->code, $u['C']->code, $u['D']->code, $u['A']->code, $u['B']->code],
            $nodes->map(static fn (LineageNodeData $n): string => $n->code)->all(),
        );

        $e = $byCode[$u['E']->code];
        Assert::assertSame(1, $e->depth);
        Assert::assertSame(1.0, $e->cumulativeShare);
        Assert::assertSame(760.0, $e->attributedKg);
        Assert::assertSame($orders[2]->id, $e->processingOrderId);
        Assert::assertNull($e->parentIndex);

        $c = $byCode[$u['C']->code];
        Assert::assertSame(2, $c->depth);
        Assert::assertSame(0.75, $c->cumulativeShare);
        Assert::assertSame(570.0, $c->attributedKg);
        Assert::assertSame($orders[1]->id, $c->processingOrderId);

        $a = $byCode[$u['A']->code];
        Assert::assertSame(3, $a->depth);
        Assert::assertSame(0.45, $a->cumulativeShare);
        Assert::assertSame(342.0, $a->attributedKg);
        Assert::assertSame($orders[0]->id, $a->processingOrderId);
        Assert::assertSame(SafeIntCastAction::cast($u['C']->getKey()), $a->viaStockUnitId);

        // Le foglie (A, B, D) coprono l'intera massa di G.
        $leaves = $byCode[$u['A']->code]->attributedKg + $byCode[$u['B']->code]->attributedKg + $byCode[$u['D']->code]->attributedKg;
        Assert::assertEqualsWithDelta(760.0, $leaves, 0.001);
    });

    test('rispetta maxDepth', function (): void {
        ['units' => $u] = threeLevelLineage();
        $nodes = app(TraceUpstreamAction::class)->execute($u['G'], 1);

        Assert::assertCount(1, $nodes);
        Assert::assertSame($u['E']->code, $nodes->first()?->code);
    });

    test('unità senza origine: nessun nodo', function (): void {
        ['units' => $u] = threeLevelLineage();
        Assert::assertCount(0, app(TraceUpstreamAction::class)->execute($u['A']));
    });

    test('un ciclo nei dati non blocca la visita', function (): void {
        ['units' => $u, 'orders' => $orders] = threeLevelLineage();
        LineageLink::factory()
            ->between($u['G'], $u['A'], 1.0, 600.0)
            ->create(['processing_order_id' => $orders[2]->id]);

        $nodes = app(TraceUpstreamAction::class)->execute($u['G']);

        Assert::assertCount(5, $nodes);
        Assert::assertNotContains($u['G']->code, $nodes->map(static fn (LineageNodeData $n): string => $n->code)->all());
    });
});

describe('TraceDownstreamAction', function (): void {
    test('scende tre livelli: quota di ogni nodo che proviene dall\'unità di partenza', function (): void {
        ['units' => $u, 'orders' => $orders] = threeLevelLineage();
        $byCode = nodesByCode(app(TraceDownstreamAction::class)->execute($u['A']));

        Assert::assertSame([$u['C']->code, $u['E']->code, $u['F']->code, $u['G']->code], array_keys($byCode));

        Assert::assertSame(0.6, $byCode[$u['C']->code]->cumulativeShare);
        Assert::assertSame(540.0, $byCode[$u['C']->code]->attributedKg);

        Assert::assertSame(0.45, $byCode[$u['E']->code]->cumulativeShare);
        Assert::assertSame(360.0, $byCode[$u['E']->code]->attributedKg);
        Assert::assertSame(135.0, $byCode[$u['F']->code]->attributedKg);

        $g = $byCode[$u['G']->code];
        Assert::assertSame(3, $g->depth);
        Assert::assertSame(0.45, $g->cumulativeShare);
        Assert::assertSame(342.0, $g->attributedKg);
        Assert::assertSame($orders[2]->id, $g->processingOrderId);
    });

    test('a monte e a valle sono coerenti: kg di A in G', function (): void {
        ['units' => $u] = threeLevelLineage();
        $down = nodesByCode(app(TraceDownstreamAction::class)->execute($u['A']));
        $up = nodesByCode(app(TraceUpstreamAction::class)->execute($u['G']));

        Assert::assertSame($up[$u['A']->code]->attributedKg, $down[$u['G']->code]->attributedKg);
        Assert::assertSame($up[$u['A']->code]->cumulativeShare, $down[$u['G']->code]->cumulativeShare);
    });
});
