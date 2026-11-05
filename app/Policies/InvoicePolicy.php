<?php

namespace App\Policies;

class InvoicePolicy extends BasePolicy
{
    protected string $permissionPrefix = 'invoices';
}
