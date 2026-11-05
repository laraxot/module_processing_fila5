<?php

declare(strict_types=1);

namespace Modules\Processing\Providers;

use Modules\Xot\Providers\XotBaseEventServiceProvider;

/**
 * Registrato da XotBaseServiceProvider::register(): deve esistere anche se vuoto.
 */
class EventServiceProvider extends XotBaseEventServiceProvider
{
    public string $name = 'Processing';

    /**
     * @var array<string, array<int, string>>
     */
    protected $listen = [];
}
