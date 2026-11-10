<?php

declare(strict_types=1);

namespace Modules\Processing\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Models\Policies\Concerns\DeniesUndeclaredFilamentAbilities;

class MachinePolicy extends UserBasePolicy
{
    use DeniesUndeclaredFilamentAbilities;

    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('machine.viewAny');
    }

    public function view(UserContract $user): bool
    {
        return $user->hasPermissionTo('machine.view');
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionTo('machine.create');
    }

    public function update(UserContract $user): bool
    {
        return $user->hasPermissionTo('machine.update');
    }

    public function delete(UserContract $user): bool
    {
        return $user->hasPermissionTo('machine.delete');
    }
}
