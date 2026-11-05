<?php

declare(strict_types=1);

namespace Modules\Processing\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\ServiceProvider;
use Modules\Processing\Providers\ProcessingServiceProvider;
use Modules\Warehouse\Providers\WarehouseServiceProvider;
use Modules\Xot\Tests\XotBaseTestCase;

/**
 * Base dei test del modulo Processing.
 *
 * Sqlite condiviso (prepareSharedSqliteForTesting): processing e warehouse usano lo stesso PDO,
 * così DatabaseTransactions annulla le scritture di entrambi i moduli insieme.
 */
abstract class TestCase extends XotBaseTestCase
{
    use DatabaseTransactions;

    /** @var list<string> */
    protected $connectionsToTransact = ['sqlite', 'processing', 'warehouse'];

    protected function setUp(): void
    {
        $this->prepareSharedSqliteForTesting();

        parent::setUp();
    }

    /**
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders(Application $app): array
    {
        return [
            ...parent::getPackageProviders($app),
            WarehouseServiceProvider::class,
            ProcessingServiceProvider::class,
        ];
    }
}
