<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class NumberGeneratorService
{
    public static function invoicePrefix(): string
    {
        return Setting::get('invoice_prefix', 'INV-');
    }

    public static function paymentPrefix(): string
    {
        return Setting::get('payment_prefix', 'PAY-');
    }

    public static function tenantPrefix(): string
    {
        return Setting::get('tenant_prefix', 'TNT-');
    }

    public static function bookingPrefix(): string
    {
        return Setting::get('booking_prefix', 'BKG-');
    }

    public static function contractPrefix(): string
    {
        return Setting::get('contract_prefix', 'CTR-');
    }

    public static function generate(string $table, string $column, string $prefix, int $padLength = 5): string
    {
        return DB::transaction(function () use ($table, $column, $prefix, $padLength) {
            $last = DB::table($table)
                ->where($column, 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc($column)
                ->value($column);

            $number = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

            return $prefix.str_pad((string) $number, $padLength, '0', STR_PAD_LEFT);
        });
    }
}
