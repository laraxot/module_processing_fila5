<?php

declare(strict_types=1);

namespace Modules\Processing\Tests\Feature;

use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Processing\Enums\ProcessingOrderStatusEnum;
use Modules\Processing\Filament\Actions\AddProcessingInputHeaderAction;
use Modules\Processing\Filament\Actions\CancelProcessingOrderHeaderAction;
use Modules\Processing\Filament\Actions\CompleteProcessingOrderHeaderAction;
use Modules\Processing\Filament\Actions\ProcessingOrderAction;
use Modules\Processing\Filament\Actions\RegisterProcessingOutputHeaderAction;
use Modules\Processing\Filament\Actions\StartProcessingOrderHeaderAction;
use Modules\Processing\Models\Policies\ProcessingOrderPolicy;
use Modules\Processing\Models\ProcessingOrder;
use Modules\Processing\Tests\TestCase;
use Modules\Xot\Contracts\UserContract;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * Utente autenticato fittizio: le abilità consentite le decide un Gate::before del test,
 * così il test non dipende da tabelle e permessi del modulo User.
 *
 * @param  list<string>  $abilities
 */
function poActingWith(array $abilities): void
{
    Auth::setUser(new class(['id' => 1]) extends GenericUser implements AuthorizableContract
    {
        use Authorizable;
    });
    Gate::before(static fn (mixed $_user, string $ability): bool => \in_array($ability, $abilities, true));
}

function poUserContract(): UserContract
{
    $user = \Mockery::mock(UserContract::class);
    if (! $user instanceof UserContract) {
        throw new \LogicException('Processing policy test double must implement UserContract.');
    }

    return $user;
}

function poOrderIn(ProcessingOrderStatusEnum $status): ProcessingOrder
{
    $order = ProcessingOrder::factory()->createOne(['status' => $status]);
    Assert::assertInstanceOf(ProcessingOrder::class, $order);

    return $order;
}

describe('punto di estensione processing.material_actions_enabled', function (): void {
    beforeEach(function (): void {
        // Nell'app completa Roosko lo spegne nel suo ServiceProvider: qui si prova Processing da solo.
        config(['processing.material_actions_enabled' => true]);
    });

    test('is enabled by default in the module configuration', function (): void {
        /** @var array<string, mixed> $config */
        $config = require \dirname(__DIR__, 2).'/config/config.php';

        Assert::assertTrue($config['material_actions_enabled']);
        Assert::assertTrue(ProcessingOrderAction::materialActionsEnabled());
    });

    test('hides "add input" and "register output" when an installation turns it off', function (): void {
        poActingWith(['addInput', 'registerOutput']);
        $order = poOrderIn(ProcessingOrderStatusEnum::IN_PROGRESS);

        Assert::assertFalse(AddProcessingInputHeaderAction::make()->record($order)->isHidden());
        Assert::assertFalse(RegisterProcessingOutputHeaderAction::make()->record($order)->isHidden());

        config(['processing.material_actions_enabled' => false]);

        Assert::assertFalse(ProcessingOrderAction::materialActionsEnabled());
        Assert::assertTrue(AddProcessingInputHeaderAction::make()->record($order)->isHidden());
        Assert::assertTrue(RegisterProcessingOutputHeaderAction::make()->record($order)->isHidden());
    });
});

describe('ogni bottone che cambia l\'ordine richiede la sua abilità', function (): void {
    test('without abilities every order button is unauthorized', function (): void {
        poActingWith([]);
        $draft = poOrderIn(ProcessingOrderStatusEnum::DRAFT);
        $running = poOrderIn(ProcessingOrderStatusEnum::IN_PROGRESS);

        Assert::assertFalse(StartProcessingOrderHeaderAction::make()->record($draft)->isAuthorized());
        Assert::assertFalse(CancelProcessingOrderHeaderAction::make()->record($draft)->isAuthorized());
        Assert::assertFalse(CompleteProcessingOrderHeaderAction::make()->record($running)->isAuthorized());
        Assert::assertFalse(AddProcessingInputHeaderAction::make()->record($running)->isAuthorized());
        Assert::assertFalse(RegisterProcessingOutputHeaderAction::make()->record($running)->isAuthorized());
    });

    test('each button is authorized by its own ability', function (): void {
        poActingWith(['start', 'cancel', 'complete', 'addInput', 'registerOutput']);
        $draft = poOrderIn(ProcessingOrderStatusEnum::DRAFT);
        $running = poOrderIn(ProcessingOrderStatusEnum::IN_PROGRESS);

        Assert::assertTrue(StartProcessingOrderHeaderAction::make()->record($draft)->isAuthorized());
        Assert::assertTrue(CancelProcessingOrderHeaderAction::make()->record($draft)->isAuthorized());
        Assert::assertTrue(CompleteProcessingOrderHeaderAction::make()->record($running)->isAuthorized());
        Assert::assertTrue(AddProcessingInputHeaderAction::make()->record($running)->isAuthorized());
        Assert::assertTrue(RegisterProcessingOutputHeaderAction::make()->record($running)->isAuthorized());
    });
});

describe('forza la chiusura', function (): void {
    test('completing does not grant forcing', function (): void {
        poActingWith(['complete']);
        $action = CompleteProcessingOrderHeaderAction::make()->record(poOrderIn(ProcessingOrderStatusEnum::IN_PROGRESS));

        Assert::assertTrue($action->allows('complete'));
        Assert::assertFalse($action->allows('forceComplete'));
    });

    test('the dedicated ability allows forcing', function (): void {
        poActingWith(['complete', 'forceComplete']);
        $action = CompleteProcessingOrderHeaderAction::make()->record(poOrderIn(ProcessingOrderStatusEnum::IN_PROGRESS));

        Assert::assertTrue($action->allows('forceComplete'));
    });

    test('the policy checks the processing_order.forceComplete permission', function (): void {
        $user = \Mockery::mock(UserContract::class);
        $user->shouldReceive('hasPermissionTo')->with('processing_order.forceComplete')->once()->andReturn(false);
        $user->shouldReceive('hasPermissionTo')->with('processing_order.complete')->once()->andReturn(true);
        if (! $user instanceof UserContract) {
            throw new \LogicException('Processing policy test double must implement UserContract.');
        }

        $policy = new ProcessingOrderPolicy;

        Assert::assertTrue($policy->complete($user));
        Assert::assertFalse($policy->forceComplete($user));
    });

    test('the policy denies the Filament abilities it does not declare', function (): void {
        $user = poUserContract();
        $policy = new ProcessingOrderPolicy;

        Assert::assertFalse($policy->deleteAny($user));
        Assert::assertFalse($policy->reorder($user));
    });
});
