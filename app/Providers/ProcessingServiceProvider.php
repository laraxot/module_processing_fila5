<?php

declare(strict_types=1);

namespace Modules\Processing\Providers;

use Modules\Xot\Providers\XotBaseServiceProvider;
use Override;

/**
 * Service Provider del modulo Processing.
 */
class ProcessingServiceProvider extends XotBaseServiceProvider
{
    public string $name = 'Processing';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;

    #[Override]
    public function boot(): void
    {
        parent::boot();

        $this->registerConfig();
    }

    /**
     * Pubblica e unisce config/config.php sotto la chiave `processing` (come Activity).
     */
    #[Override]
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->name, 'config/config.php') => config_path('processing.php'),
        ], 'config');

        $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), 'processing');
    }
}
