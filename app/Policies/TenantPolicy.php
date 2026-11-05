<?php

namespace App\Policies;

class TenantPolicy extends BasePolicy
{
    protected string $permissionPrefix = 'tenants';
}
