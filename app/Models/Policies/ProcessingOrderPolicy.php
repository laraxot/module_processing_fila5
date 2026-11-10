<?php

declare(strict_types=1);

namespace Modules\Processing\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Models\Policies\Concerns\DeniesUndeclaredFilamentAbilities;
use Modules\Xot\Models\Policies\Concerns\RequiresOperationalPermissions;

/**
 * Oltre al CRUD, un'abilità per ogni bottone che cambia l'ordine (Filament actions di Processing):
 * avvio, chiusura, chiusura forzata oltre la soglia di perdita (riservata al responsabile),
 * annullamento, aggiunta di input e registrazione di output.
 */
class ProcessingOrderPolicy extends UserBasePolicy
{
    use DeniesUndeclaredFilamentAbilities;
    use RequiresOperationalPermissions;

    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_order.viewAny');
    }

    public function view(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_order.view');
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_order.create');
    }

    public function update(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_order.update');
    }

    public function delete(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_order.delete');
    }

    public function start(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_order.start');
    }

    public function complete(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_order.complete');
    }

    /** Chiusura oltre la soglia `processing.max_loss_pct`: abilità distinta da `complete`. */
    public function forceComplete(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_order.forceComplete');
    }

    public function cancel(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_order.cancel');
    }

    public function addInput(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_order.addInput');
    }

    public function registerOutput(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_order.registerOutput');
    }
}
