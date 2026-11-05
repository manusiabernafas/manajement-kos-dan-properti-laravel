<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public const MODULES = [
        'properties', 'buildings', 'floors', 'room-types', 'rooms', 'facilities',
        'tenants', 'tenant-documents', 'bookings', 'check-ins', 'check-outs', 'contracts',
        'invoices', 'payments', 'deposits', 'penalties', 'meters', 'expenses',
        'maintenance', 'complaints', 'inventories', 'announcements', 'reports',
        'users', 'roles', 'audit-logs', 'settings',
    ];

    public const EXTRA_PERMISSIONS = [
        'payments.verify',
        'invoices.cancel',
        'bookings.confirm',
        'check-ins.process',
        'check-outs.process',
    ];

    public function run(): void
    {
        $permissions = [];

        foreach (self::MODULES as $module) {
            foreach (['view', 'create', 'update', 'delete'] as $action) {
                $permissions[] = "{$module}.{$action}";
            }
        }

        $permissions = array_merge($permissions, self::EXTRA_PERMISSIONS);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $owner = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $owner->syncPermissions(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions($this->managerPermissions());

        $finance = Role::firstOrCreate(['name' => 'Finance', 'guard_name' => 'web']);
        $finance->syncPermissions($this->financePermissions());

        $staff = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->syncPermissions($this->staffPermissions());

        $technician = Role::firstOrCreate(['name' => 'Technician', 'guard_name' => 'web']);
        $technician->syncPermissions($this->technicianPermissions());

        Role::firstOrCreate(['name' => 'Tenant', 'guard_name' => 'web']);
    }

    protected function managerPermissions(): array
    {
        return [
            'properties.view',
            'buildings.view', 'buildings.create', 'buildings.update',
            'floors.view', 'floors.create', 'floors.update',
            'room-types.view', 'room-types.create', 'room-types.update',
            'rooms.view', 'rooms.create', 'rooms.update', 'rooms.delete',
            'facilities.view', 'facilities.create', 'facilities.update',
            'tenants.view', 'tenants.create', 'tenants.update',
            'tenant-documents.view', 'tenant-documents.create', 'tenant-documents.delete',
            'bookings.view', 'bookings.create', 'bookings.update', 'bookings.confirm', 'bookings.delete',
            'check-ins.view', 'check-ins.create', 'check-ins.process',
            'check-outs.view', 'check-outs.create', 'check-outs.process',
            'contracts.view', 'contracts.create', 'contracts.update',
            'invoices.view', 'invoices.create', 'invoices.update',
            'payments.view', 'payments.create', 'payments.verify',
            'deposits.view', 'deposits.update',
            'penalties.view',
            'meters.view', 'meters.create', 'meters.update',
            'expenses.view', 'expenses.create', 'expenses.update',
            'maintenance.view', 'maintenance.create', 'maintenance.update', 'maintenance.delete',
            'complaints.view', 'complaints.update',
            'inventories.view', 'inventories.create', 'inventories.update', 'inventories.delete',
            'announcements.view', 'announcements.create', 'announcements.update', 'announcements.delete',
            'reports.view',
        ];
    }

    protected function financePermissions(): array
    {
        return [
            'properties.view', 'rooms.view', 'tenants.view', 'contracts.view',
            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.cancel',
            'payments.view', 'payments.create', 'payments.verify',
            'deposits.view', 'deposits.create', 'deposits.update',
            'penalties.view', 'penalties.create', 'penalties.update',
            'expenses.view', 'expenses.create', 'expenses.update', 'expenses.delete',
            'meters.view',
            'reports.view',
        ];
    }

    protected function staffPermissions(): array
    {
        return [
            'properties.view', 'rooms.view',
            'tenants.view', 'tenants.create', 'tenants.update',
            'tenant-documents.view', 'tenant-documents.create',
            'bookings.view', 'bookings.create', 'bookings.update', 'bookings.confirm',
            'check-ins.view', 'check-ins.create', 'check-ins.process',
            'check-outs.view', 'check-outs.create', 'check-outs.process',
            'contracts.view',
            'invoices.view', 'payments.view', 'payments.create',
            'meters.view', 'meters.create', 'meters.update',
            'maintenance.view', 'maintenance.create', 'maintenance.update',
            'complaints.view', 'complaints.create', 'complaints.update',
            'inventories.view',
            'announcements.view', 'announcements.create',
            'reports.view',
        ];
    }

    protected function technicianPermissions(): array
    {
        return [
            'rooms.view',
            'maintenance.view', 'maintenance.update',
            'complaints.view', 'complaints.update',
            'inventories.view', 'inventories.update',
        ];
    }
}
