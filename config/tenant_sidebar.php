<?php

return [
    [
        'title' => null,
        'items' => [
            ['label' => 'Dashboard', 'route' => 'tenant.dashboard', 'icon' => 'fa-solid fa-grid-2'],
            ['label' => 'Kamar Saya', 'route' => 'tenant.room', 'icon' => 'fa-solid fa-door-open'],
            ['label' => 'Kontrak Saya', 'route' => 'tenant.contract', 'icon' => 'fa-solid fa-file-signature'],
            ['label' => 'Tagihan', 'route' => 'tenant.invoices.index', 'icon' => 'fa-solid fa-file-invoice-dollar'],
            ['label' => 'Pembayaran', 'route' => 'tenant.payments.index', 'icon' => 'fa-solid fa-money-check-dollar'],
            ['label' => 'Maintenance', 'route' => 'tenant.maintenance.index', 'icon' => 'fa-solid fa-screwdriver-wrench'],
            ['label' => 'Komplain', 'route' => 'tenant.complaints.index', 'icon' => 'fa-solid fa-comment-dots'],
            ['label' => 'Pengumuman', 'route' => 'tenant.announcements.index', 'icon' => 'fa-solid fa-bullhorn'],
            ['label' => 'Profil', 'route' => 'profile.edit', 'icon' => 'fa-solid fa-user'],
        ],
    ],
];
