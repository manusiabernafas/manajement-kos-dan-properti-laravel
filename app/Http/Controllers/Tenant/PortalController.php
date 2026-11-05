<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant as TenantModel;
use Illuminate\Http\Request;

abstract class PortalController extends Controller
{
    protected function tenant(Request $request): TenantModel
    {
        $tenant = $request->user()->tenant;

        abort_unless($tenant, 403, 'Akun Anda belum terhubung dengan data penghuni.');

        return $tenant;
    }
}
