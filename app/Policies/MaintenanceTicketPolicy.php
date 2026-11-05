<?php

namespace App\Policies;

class MaintenanceTicketPolicy extends BasePolicy
{
    protected string $permissionPrefix = 'maintenance';
}
