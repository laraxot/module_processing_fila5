<?php

declare(strict_types=1);

namespace Modules\Processing\Tests\Unit;

use Modules\Processing\Actions\CalculateMassBalanceAction;
use Modules\Processing\Tests\TestCase;
use PHPUnit\Framework\Assert;
use Webmozart\Assert\InvalidArgumentException;

uses(TestCase::class);

describe('CalculateMassBalanceAction', function (): void {
    test('bilancio 1→1 con perdita', function (): void {
        $balance = (new CalculateMassBalanceAction)->execute([1000.0], [900.0]);

        Assert::assertSame(1000.0, $balance->inputKg);
        Assert::assertSame(900.0, $balance->outputKg);
        Assert::assertSame(100.0, $balance->lossKg);
        Assert::assertSame(10.0, $balance->lossPct);
    });

    test('somma più input e più output', function (): void {
        $balance = (new CalculateMassBalanceAction)->execute([600.0, 400.0], [700.0, 250.0]);

        Assert::assertSame(1000.0, $balance->inputKg);
        Assert::assertSame(950.0, $balance->outputKg);
        Assert::assertSame(50.0, $balance->lossKg);
        Assert::assertSame(5.0, $balance->lossPct);
    });

    test('arrotonda al grammo senza errori di virgola mobile', function (): void {
        $balance = (new CalculateMassBalanceAction)->execute([0.1, 0.2], [0.3]);

        Assert::assertSame(0.3, $balance->inputKg);
        Assert::assertSame(0.0, $balance->lossKg);
        Assert::assertSame(0.0, $balance->lossPct);
    });

    test('output superiore all\'input dà perdita negativa', function (): void {
        $balance = (new CalculateMassBalanceAction)->execute([1000.0], [1020.0]);

        Assert::assertSame(-20.0, $balance->lossKg);
        Assert::assertSame(-2.0, $balance->lossPct);
        Assert::assertFalse($balance->exceeds(15.0));
    });

    test('senza input la percentuale è zero', function (): void {
        $balance = (new CalculateMassBalanceAction)->execute([], [10.0]);

        Assert::assertSame(0.0, $balance->lossPct);
    });

    test('perdita oltre soglia', function (): void {
        $balance = (new CalculateMassBalanceAction)->execute([1000.0], [800.0]);

        Assert::assertSame(20.0, $balance->lossPct);
        Assert::assertTrue($balance->exceeds(15.0));
        Assert::assertFalse($balance->exceeds(20.0));
    });

    test('rifiuta pesi negativi', function (): void {
        expect(fn () => (new CalculateMassBalanceAction)->execute([-1.0], [1.0]))
            ->toThrow(InvalidArgumentException::class);
    });
});
