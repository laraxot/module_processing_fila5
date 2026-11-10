<?php

declare(strict_types=1);

namespace Modules\Processing\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Models\Policies\Concerns\DeniesUndeclaredFilamentAbilities;

class ProcessingOutputPolicy extends UserBasePolicy
{
    use DeniesUndeclaredFilamentAbilities;

    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_output.viewAny');
    }

    public function view(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_output.view');
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionTo('processing_output.create');
    }

    /** Record immutabile: mai modificabile da UI. */
    public function update(UserContract $_user): bool
    {
        return false;
    }

    /** Record immutabile: mai eliminabile da UI. */
    public function delete(UserContract $_user): bool
    {
        return false;
    }
}
