<?php

declare(strict_types=1);

namespace Modules\Processing\Tests\Unit;

use Modules\Processing\Actions\CalculateLineageSharesAction;
use Modules\Processing\Datas\LineageShareData;
use Modules\Processing\Exceptions\InvalidMaterialException;
use Modules\Processing\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * @param  list<LineageShareData>  $links
 * @return array<string, array{share: float, kg: float}>
 */
function lineageByPair(array $links): array
{
    $map = [];
    foreach ($links as $link) {
        $map[$link->parentStockUnitId.'>'.$link->childStockUnitId] = ['share' => $link->share, 'kg' => $link->attributedKg];
    }

    return $map;
}

describe('CalculateLineageSharesAction', function (): void {
    test('1→1: quota intera, tutto l\'output attribuito all\'unico input', function (): void {
        $links = lineageByPair((new CalculateLineageSharesAction)->execute([1 => 1000.0], [10 => 950.0]));

        Assert::assertSame(['1>10' => ['share' => 1.0, 'kg' => 950.0]], $links);
    });

    test('2→1: quote proporzionali agli input', function (): void {
        $links = lineageByPair((new CalculateLineageSharesAction)->execute([1 => 750.0, 2 => 250.0], [10 => 900.0]));

        Assert::assertSame(0.75, $links['1>10']['share']);
        Assert::assertSame(675.0, $links['1>10']['kg']);
        Assert::assertSame(0.25, $links['2>10']['share']);
        Assert::assertSame(225.0, $links['2>10']['kg']);
        Assert::assertSame(900.0, $links['1>10']['kg'] + $links['2>10']['kg']);
    });

    test('2→2: un arco per ogni coppia, la somma per output è il peso dell\'output', function (): void {
        $links = lineageByPair((new CalculateLineageSharesAction)->execute(
            [1 => 500.0, 2 => 500.0],
            [10 => 600.0, 11 => 300.0],
        ));

        Assert::assertCount(4, $links);
        foreach (['1>10', '2>10', '1>11', '2>11'] as $pair) {
            Assert::assertSame(0.5, $links[$pair]['share']);
        }
        Assert::assertSame(300.0, $links['1>10']['kg']);
        Assert::assertSame(150.0, $links['2>11']['kg']);
        Assert::assertSame(600.0, $links['1>10']['kg'] + $links['2>10']['kg']);
        Assert::assertSame(300.0, $links['1>11']['kg'] + $links['2>11']['kg']);
    });

    test('quote non periodiche arrotondate a 6 decimali', function (): void {
        $links = lineageByPair((new CalculateLineageSharesAction)->execute([1 => 1.0, 2 => 2.0], [10 => 3.0]));

        Assert::assertSame(0.333333, $links['1>10']['share']);
        Assert::assertSame(1.0, $links['1>10']['kg']);
        Assert::assertSame(0.666667, $links['2>10']['share']);
        Assert::assertSame(2.0, $links['2>10']['kg']);
    });

    test('totale input zero non è calcolabile', function (): void {
        expect(fn () => (new CalculateLineageSharesAction)->execute([1 => 0.0], [10 => 1.0]))
            ->toThrow(InvalidMaterialException::class);
    });
});
