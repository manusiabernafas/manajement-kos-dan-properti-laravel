<?php

namespace App\Policies;

use App\Models\User;

class PaymentPolicy extends BasePolicy
{
    protected string $permissionPrefix = 'payments';

    public function verify(User $user): bool
    {
        return $user->can('payments.verify');
    }
}
