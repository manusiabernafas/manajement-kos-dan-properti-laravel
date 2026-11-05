<?php

return [
    [
        'title' => null,
        'items' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fa-solid fa-grid-2', 'permission' => null],
        ],
    ],
    [
        'title' => 'Properti',
        'items' => [
            ['label' => 'Properti', 'route' => 'properties.index', 'icon' => 'fa-solid fa-city', 'permission' => 'properties.view'],
            ['label' => 'Gedung', 'route' => 'buildings.index', 'icon' => 'fa-solid fa-building', 'permission' => 'buildings.view'],
            ['label' => 'Lantai', 'route' => 'floors.index', 'icon' => 'fa-solid fa-layer-group', 'permission' => 'floors.view'],
            ['label' => 'Tipe Kamar', 'route' => 'room-types.index', 'icon' => 'fa-solid fa-bed', 'permission' => 'room-types.view'],
            ['label' => 'Kamar', 'route' => 'rooms.index', 'icon' => 'fa-solid fa-door-open', 'permission' => 'rooms.view'],
            ['label' => 'Fasilitas', 'route' => 'facilities.index', 'icon' => 'fa-solid fa-wifi', 'permission' => 'facilities.view'],
        ],
    ],
    [
        'title' => 'Penghuni',
        'items' => [
            ['label' => 'Penghuni', 'route' => 'tenants.index', 'icon' => 'fa-solid fa-users', 'permission' => 'tenants.view'],
            ['label' => 'Booking', 'route' => 'bookings.index', 'icon' => 'fa-solid fa-calendar-check', 'permission' => 'bookings.view'],
            ['label' => 'Check-in', 'route' => 'check-ins.index', 'icon' => 'fa-solid fa-right-to-bracket', 'permission' => 'check-ins.view'],
            ['label' => 'Check-out', 'route' => 'check-outs.index', 'icon' => 'fa-solid fa-right-from-bracket', 'permission' => 'check-outs.view'],
            ['label' => 'Kontrak', 'route' => 'contracts.index', 'icon' => 'fa-solid fa-file-signature', 'permission' => 'contracts.view'],
        ],
    ],
    [
        'title' => 'Keuangan',
        'items' => [
            ['label' => 'Tagihan', 'route' => 'invoices.index', 'icon' => 'fa-solid fa-file-invoice-dollar', 'permission' => 'invoices.view'],
            ['label' => 'Pembayaran', 'route' => 'payments.index', 'icon' => 'fa-solid fa-money-check-dollar', 'permission' => 'payments.view'],
            ['label' => 'Deposit', 'route' => 'deposits.index', 'icon' => 'fa-solid fa-piggy-bank', 'permission' => 'deposits.view'],
            ['label' => 'Denda', 'route' => 'penalties.index', 'icon' => 'fa-solid fa-hand-holding-dollar', 'permission' => 'penalties.view'],
            ['label' => 'Pengeluaran', 'route' => 'expenses.index', 'icon' => 'fa-solid fa-receipt', 'permission' => 'expenses.view'],
        ],
    ],
    [
        'title' => 'Operasional',
        'items' => [
            ['label' => 'Meter', 'route' => 'meters.index', 'icon' => 'fa-solid fa-gauge-high', 'permission' => 'meters.view'],
            ['label' => 'Maintenance', 'route' => 'maintenance.index', 'icon' => 'fa-solid fa-screwdriver-wrench', 'permission' => 'maintenance.view'],
            ['label' => 'Komplain', 'route' => 'complaints.index', 'icon' => 'fa-solid fa-comment-dots', 'permission' => 'complaints.view'],
            ['label' => 'Inventaris', 'route' => 'inventories.index', 'icon' => 'fa-solid fa-boxes-stacked', 'permission' => 'inventories.view'],
            ['label' => 'Pengumuman', 'route' => 'announcements.index', 'icon' => 'fa-solid fa-bullhorn', 'permission' => 'announcements.view'],
        ],
    ],
    [
        'title' => 'Laporan',
        'items' => [
            ['label' => 'Occupancy', 'route' => 'reports.index', 'params' => ['type' => 'occupancy'], 'icon' => 'fa-solid fa-chart-pie', 'permission' => 'reports.view'],
            ['label' => 'Pendapatan', 'route' => 'reports.index', 'params' => ['type' => 'revenue'], 'icon' => 'fa-solid fa-arrow-trend-up', 'permission' => 'reports.view'],
            ['label' => 'Pengeluaran', 'route' => 'reports.index', 'params' => ['type' => 'expense'], 'icon' => 'fa-solid fa-arrow-trend-down', 'permission' => 'reports.view'],
            ['label' => 'Profit & Loss', 'route' => 'reports.index', 'params' => ['type' => 'profit-loss'], 'icon' => 'fa-solid fa-scale-balanced', 'permission' => 'reports.view'],
            ['label' => 'Pembayaran', 'route' => 'reports.index', 'params' => ['type' => 'payment'], 'icon' => 'fa-solid fa-credit-card', 'permission' => 'reports.view'],
            ['label' => 'Piutang', 'route' => 'reports.index', 'params' => ['type' => 'outstanding'], 'icon' => 'fa-solid fa-file-invoice', 'permission' => 'reports.view'],
            ['label' => 'Penghuni', 'route' => 'reports.index', 'params' => ['type' => 'tenant'], 'icon' => 'fa-solid fa-users', 'permission' => 'reports.view'],
            ['label' => 'Kamar', 'route' => 'reports.index', 'params' => ['type' => 'room'], 'icon' => 'fa-solid fa-door-open', 'permission' => 'reports.view'],
        ],
    ],
    [
        'title' => 'Sistem',
        'items' => [
            ['label' => 'Users', 'route' => 'users.index', 'icon' => 'fa-solid fa-user-gear', 'permission' => 'users.view'],
            ['label' => 'Roles & Permissions', 'route' => 'roles.index', 'icon' => 'fa-solid fa-shield-halved', 'permission' => 'roles.view'],
            ['label' => 'Audit Logs', 'route' => 'audit-logs.index', 'icon' => 'fa-solid fa-clipboard-list', 'permission' => 'audit-logs.view'],
            ['label' => 'Pengaturan', 'route' => 'settings.index', 'icon' => 'fa-solid fa-gear', 'permission' => 'settings.view'],
        ],
    ],
];
