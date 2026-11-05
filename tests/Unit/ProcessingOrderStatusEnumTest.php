<?php

declare(strict_types=1);

namespace Modules\Processing\Tests\Unit;

use Modules\Processing\Enums\ProcessingOrderStatusEnum as S;
use Modules\Processing\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('ProcessingOrderStatusEnum::canTransitionTo', function (): void {
    test('transizioni consentite', function (S $from, S $to): void {
        Assert::assertTrue($from->canTransitionTo($to));
    })->with([
        [S::DRAFT, S::IN_PROGRESS],
        [S::DRAFT, S::CANCELLED],
        [S::IN_PROGRESS, S::COMPLETED],
        [S::IN_PROGRESS, S::CANCELLED],
    ]);

    test('transizioni vietate', function (S $from, S $to): void {
        Assert::assertFalse($from->canTransitionTo($to));
    })->with([
        [S::DRAFT, S::COMPLETED],
        [S::DRAFT, S::DRAFT],
        [S::IN_PROGRESS, S::DRAFT],
        [S::COMPLETED, S::CANCELLED],
        [S::COMPLETED, S::IN_PROGRESS],
        [S::CANCELLED, S::DRAFT],
        [S::CANCELLED, S::IN_PROGRESS],
    ]);

    test('solo un ordine avviato accetta materiale', function (): void {
        Assert::assertSame(
            [S::IN_PROGRESS],
            array_values(array_filter(S::cases(), static fn (S $s): bool => $s->acceptsMaterial())),
        );
    });
});
