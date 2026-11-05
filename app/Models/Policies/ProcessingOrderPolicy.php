<?php

declare(strict_types=1);

namespace Modules\Processing\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Xot\Contracts\UserContract;

class ProcessingOrderPolicy extends UserBasePolicy
{
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
}
