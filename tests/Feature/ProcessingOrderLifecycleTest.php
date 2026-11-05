<?php

declare(strict_types=1);

namespace Modules\Processing\Tests\Feature;

use Modules\Processing\Actions\AddProcessingInputAction;
use Modules\Processing\Actions\CancelProcessingOrderAction;
use Modules\Processing\Actions\CompleteProcessingOrderAction;
use Modules\Processing\Actions\CreateProcessingOrderAction;
use Modules\Processing\Actions\RegisterProcessingOutputAction;
use Modules\Processing\Actions\StartProcessingOrderAction;
use Modules\Processing\Actions\TraceUpstreamAction;
use Modules\Processing\Enums\MachineTypeEnum;
use Modules\Processing\Enums\OutputKindEnum;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Exceptions\ExcessiveMassLossException;
use Modules\Processing\Exceptions\ImmutableRecordException;
use Modules\Processing\Exceptions\InactiveMachineException;
use Modules\Processing\Exceptions\IncompleteProcessingOrderException;
use Modules\Processing\Exceptions\InvalidMaterialException;
use Modules\Processing\Exceptions\InvalidOrderStateException;
use Modules\Processing\Exceptions\OrderNotCancellableException;
use Modules\Processing\Models\LineageLink;
use Modules\Processing\Models\Machine;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Processing\Tests\TestCase;
use Modules\Warehouse\Actions\CreateStockUnitAction;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Models\StockMovement;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Models\Warehouse;
use PHPUnit\Framework\Assert;
use Webmozart\Assert\InvalidArgumentException;

uses(TestCase::class);

beforeEach(function (): void {
    config(['processing.max_loss_pct' => 15.0]);
});

function poWarehouse(): Warehouse
{
    $warehouse = Warehouse::factory()->create();
    Assert::assertInstanceOf(Warehouse::class, $warehouse);

    return $warehouse;
}

function poMachine(): Machine
{
    $machine = Machine::factory()->ofType(MachineTypeEnum::SHREDDER)->create();
    Assert::assertInstanceOf(Machine::class, $machine);

    return $machine;
}

function poUnitData(Warehouse $warehouse, float $kg): StockUnitData
{
    return new StockUnitData(
        warehouseId: (int) $warehouse->getKey(),
        containerType: ContainerTypeEnum::BIG_BAG,
        netWeightKg: $kg,
    );
}

/** Unità ricevuta in magazzino (movimento receipt), pronta per essere lavorata. */
function poReceive(Warehouse $warehouse, float $kg): StockUnit
{
    return app(CreateStockUnitAction::class)->execute(poUnitData($warehouse, $kg));
}

function poStartedOrder(Machine $machine): ProcessingOrder
{
    $order = app(CreateProcessingOrderAction::class)->execute($machine, 'R12');

    return app(StartProcessingOrderAction::class)->execute($order);
}

describe('ciclo completo di un ordine di lavorazione', function (): void {
    test('crea, avvia, consuma 2 unità, produce 2 unità, completa con bilancio e genealogia', function (): void {
        [$w, $m] = [poWarehouse(), poMachine()];
        $a = poReceive($w, 600.0);
        $b = poReceive($w, 400.0);

        $order = app(CreateProcessingOrderAction::class)->execute($m, 'R12', now()->addHour(), 'demo');
        Assert::assertMatchesRegularExpression('/^PO-'.now()->format('Y').'-\d{6}$/', $order->code);
        Assert::assertSame(ProcessingOrderStatusEnum::DRAFT, $order->status);

        $order = app(StartProcessingOrderAction::class)->execute($order);
        Assert::assertSame(ProcessingOrderStatusEnum::IN_PROGRESS, $order->status);
        Assert::assertNotNull($order->started_at);

        $inputA = app(AddProcessingInputAction::class)->execute($order, $a);
        $inputB = app(AddProcessingInputAction::class)->execute($order, $b);
        Assert::assertSame('600.000', $inputA->quantity_kg);
        Assert::assertSame('400.000', $inputB->quantity_kg);

        // Consumo registrato in Warehouse con reference all'ordine; unità svuotata.
        $consumption = StockMovement::query()
            ->where('stock_unit_id', $a->getKey())
            ->where('type', MovementTypeEnum::CONSUMPTION)
            ->first();
        Assert::assertNotNull($consumption);
        Assert::assertSame(0.0, (float) $a->fresh()?->net_weight_kg);

        $product = app(RegisterProcessingOutputAction::class)->execute($order, poUnitData($w, 700.0));
        $scrap = app(RegisterProcessingOutputAction::class)->execute($order, poUnitData($w, 250.0), OutputKindEnum::SCRAP);
        Assert::assertSame(OutputKindEnum::PRODUCT, $product->kind);
        Assert::assertSame(OutputKindEnum::SCRAP, $scrap->kind);
        Assert::assertTrue(StockMovement::query()
            ->where('stock_unit_id', $product->stock_unit_id)
            ->where('type', MovementTypeEnum::PRODUCTION)
            ->exists());

        $order = app(CompleteProcessingOrderAction::class)->execute($order);

        Assert::assertSame(ProcessingOrderStatusEnum::COMPLETED, $order->status);
        Assert::assertNotNull($order->completed_at);
        Assert::assertSame('1000.000', $order->input_kg);
        Assert::assertSame('950.000', $order->output_kg);
        Assert::assertSame('50.000', $order->loss_kg);
        Assert::assertSame('5.000', $order->loss_pct);

        $links = LineageLink::query()->where('processing_order_id', $order->id)->get();
        Assert::assertCount(4, $links);
        $productFromA = $links->first(static fn (LineageLink $l): bool => $l->parent_stock_unit_id === (int) $a->getKey()
            && $l->child_stock_unit_id === $product->stock_unit_id);
        Assert::assertNotNull($productFromA);
        Assert::assertSame('0.600000', $productFromA->share);
        Assert::assertSame('420.000', $productFromA->attributed_kg);

        // Tracciabilità a monte del prodotto: i due input con le loro quote.
        $productUnit = StockUnit::query()->findOrFail($product->stock_unit_id);
        $upstream = app(TraceUpstreamAction::class)->execute($productUnit);
        Assert::assertSame([$a->code, $b->code], $upstream->pluck('code')->all());
        Assert::assertSame([420.0, 280.0], $upstream->pluck('attributedKg')->all());
    });

    test('input parziale lascia il residuo nell\'unità', function (): void {
        [$w, $m] = [poWarehouse(), poMachine()];
        $unit = poReceive($w, 1000.0);
        $order = poStartedOrder($m);

        $input = app(AddProcessingInputAction::class)->execute($order, $unit, 250.0);

        Assert::assertSame('250.000', $input->quantity_kg);
        Assert::assertSame(750.0, (float) $unit->fresh()?->net_weight_kg);
    });

    test('perdita oltre soglia: rifiuto senza scritture, poi chiusura forzata', function (): void {
        [$w, $m] = [poWarehouse(), poMachine()];
        $order = poStartedOrder($m);
        app(AddProcessingInputAction::class)->execute($order, poReceive($w, 1000.0));
        app(RegisterProcessingOutputAction::class)->execute($order, poUnitData($w, 800.0));

        try {
            app(CompleteProcessingOrderAction::class)->execute($order);
            Assert::fail('Attesa ExcessiveMassLossException');
        } catch (ExcessiveMassLossException $e) {
            Assert::assertSame(20.0, $e->balance->lossPct);
            Assert::assertSame(15.0, $e->maxLossPct);
        }
        Assert::assertSame(ProcessingOrderStatusEnum::IN_PROGRESS, $order->fresh()?->status);
        Assert::assertSame(0, LineageLink::query()->where('processing_order_id', $order->id)->count());

        $order = app(CompleteProcessingOrderAction::class)->execute($order, force: true);
        Assert::assertSame(ProcessingOrderStatusEnum::COMPLETED, $order->status);
        Assert::assertSame('20.000', $order->loss_pct);
    });

    test('la soglia è letta dalla config', function (): void {
        [$w, $m] = [poWarehouse(), poMachine()];
        config(['processing.max_loss_pct' => 25.0]);
        $order = poStartedOrder($m);
        app(AddProcessingInputAction::class)->execute($order, poReceive($w, 1000.0));
        app(RegisterProcessingOutputAction::class)->execute($order, poUnitData($w, 800.0));

        Assert::assertSame(ProcessingOrderStatusEnum::COMPLETED, app(CompleteProcessingOrderAction::class)->execute($order)->status);
    });
});

describe('regole di stato e di materiale', function (): void {
    test('in bozza non accetta input', function (): void {
        [$w, $m] = [poWarehouse(), poMachine()];
        $order = app(CreateProcessingOrderAction::class)->execute($m);

        expect(fn () => app(AddProcessingInputAction::class)->execute($order, poReceive($w, 100.0)))
            ->toThrow(InvalidOrderStateException::class);
    });

    test('non si completa senza input o senza output', function (): void {
        [$w, $m] = [poWarehouse(), poMachine()];
        $order = poStartedOrder($m);
        expect(fn () => app(CompleteProcessingOrderAction::class)->execute($order))
            ->toThrow(IncompleteProcessingOrderException::class);

        app(AddProcessingInputAction::class)->execute($order, poReceive($w, 100.0));
        expect(fn () => app(CompleteProcessingOrderAction::class)->execute($order))
            ->toThrow(IncompleteProcessingOrderException::class);
    });

    test('non si consuma più del disponibile', function (): void {
        [$w, $m] = [poWarehouse(), poMachine()];
        $order = poStartedOrder($m);

        expect(fn () => app(AddProcessingInputAction::class)->execute($order, poReceive($w, 100.0), 100.5))
            ->toThrow(InvalidMaterialException::class);
    });

    test('un output dell\'ordine non può esserne input', function (): void {
        [$w, $m] = [poWarehouse(), poMachine()];
        $order = poStartedOrder($m);
        $output = app(RegisterProcessingOutputAction::class)->execute($order, poUnitData($w, 100.0));
        $unit = StockUnit::query()->findOrFail($output->stock_unit_id);

        expect(fn () => app(AddProcessingInputAction::class)->execute($order, $unit))
            ->toThrow(InvalidMaterialException::class);
    });

    test('un ordine completato non si riavvia né si annulla', function (): void {
        [$w, $m] = [poWarehouse(), poMachine()];
        $order = poStartedOrder($m);
        app(AddProcessingInputAction::class)->execute($order, poReceive($w, 100.0));
        app(RegisterProcessingOutputAction::class)->execute($order, poUnitData($w, 95.0));
        $order = app(CompleteProcessingOrderAction::class)->execute($order);

        expect(fn () => app(StartProcessingOrderAction::class)->execute($order))->toThrow(InvalidArgumentException::class);
        expect(fn () => app(CancelProcessingOrderAction::class)->execute($order, 'errore'))->toThrow(InvalidArgumentException::class);
    });

    test('annullamento: consentito senza materiale, rifiutato con input consumati', function (): void {
        [$w, $m] = [poWarehouse(), poMachine()];
        $draft = app(CreateProcessingOrderAction::class)->execute($m);
        $cancelled = app(CancelProcessingOrderAction::class)->execute($draft, 'pianificazione errata');
        Assert::assertSame(ProcessingOrderStatusEnum::CANCELLED, $cancelled->status);
        Assert::assertSame('pianificazione errata', $cancelled->cancel_reason);
        Assert::assertNotNull($cancelled->cancelled_at);

        $order = poStartedOrder($m);
        app(AddProcessingInputAction::class)->execute($order, poReceive($w, 100.0));
        expect(fn () => app(CancelProcessingOrderAction::class)->execute($order, 'ripensamento'))
            ->toThrow(OrderNotCancellableException::class);
    });

    test('i codici ordine sono progressivi nell\'anno', function (): void {
        $m = poMachine();
        $first = app(CreateProcessingOrderAction::class)->execute($m);
        $second = app(CreateProcessingOrderAction::class)->execute($m);

        $progressive = static fn (ProcessingOrder $o): int => (int) substr($o->code, -6);
        Assert::assertSame($progressive($first) + 1, $progressive($second));
    });

    test('macchina disattivata: nessun nuovo ordine', function (): void {
        $machine = Machine::factory()->inactive()->create();
        Assert::assertInstanceOf(Machine::class, $machine);

        expect(fn () => app(CreateProcessingOrderAction::class)->execute($machine))
            ->toThrow(InactiveMachineException::class);
    });

    test('input, output e genealogia sono immutabili', function (): void {
        [$w, $m] = [poWarehouse(), poMachine()];
        $order = poStartedOrder($m);
        $input = app(AddProcessingInputAction::class)->execute($order, poReceive($w, 100.0));

        expect(fn () => $input->update(['quantity_kg' => 1]))->toThrow(ImmutableRecordException::class);
        expect(fn () => $input->delete())->toThrow(ImmutableRecordException::class);
    });
});
