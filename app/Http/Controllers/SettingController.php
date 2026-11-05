<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public const KEYS = [
        'app_name' => ['default' => 'KostManager', 'group' => 'general'],
        'company_name' => ['default' => '', 'group' => 'general'],
        'company_address' => ['default' => '', 'group' => 'general'],
        'company_phone' => ['default' => '', 'group' => 'general'],
        'company_email' => ['default' => '', 'group' => 'general'],
        'currency' => ['default' => 'IDR', 'group' => 'localization'],
        'timezone' => ['default' => 'Asia/Jakarta', 'group' => 'localization'],
        'date_format' => ['default' => 'd/m/Y', 'group' => 'localization'],
        'invoice_prefix' => ['default' => 'INV-', 'group' => 'prefixes'],
        'payment_prefix' => ['default' => 'PAY-', 'group' => 'prefixes'],
        'tenant_prefix' => ['default' => 'TNT-', 'group' => 'prefixes'],
        'booking_prefix' => ['default' => 'BKG-', 'group' => 'prefixes'],
        'contract_prefix' => ['default' => 'CTR-', 'group' => 'prefixes'],
        'default_billing_day' => ['default' => '1', 'group' => 'billing'],
        'default_theme' => ['default' => 'system', 'group' => 'theme'],
    ];

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('settings.view'), 403);

        $values = [];
        foreach (self::KEYS as $key => $meta) {
            $values[$key] = Setting::get($key, $meta['default']);
        }

        return view('settings.index', ['title' => 'Pengaturan', 'values' => $values]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('settings.update'), 403);

        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'company_address' => ['nullable', 'string'],
            'company_phone' => ['nullable', 'string', 'max:30'],
            'company_email' => ['nullable', 'email', 'max:150'],
            'currency' => ['required', 'string', 'max:10'],
            'timezone' => ['required', 'string', 'max:50'],
            'date_format' => ['required', 'string', 'max:20'],
            'invoice_prefix' => ['required', 'string', 'max:20'],
            'payment_prefix' => ['required', 'string', 'max:20'],
            'tenant_prefix' => ['required', 'string', 'max:20'],
            'booking_prefix' => ['required', 'string', 'max:20'],
            'contract_prefix' => ['required', 'string', 'max:20'],
            'default_billing_day' => ['required', 'integer', 'min:1', 'max:31'],
            'default_theme' => ['required', 'in:light,dark,system'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'string', self::KEYS[$key]['group']);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
