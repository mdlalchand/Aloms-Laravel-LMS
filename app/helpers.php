<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('bd_money')) {
    function bd_money($amount): string
    {
        return '৳'.number_format((float) $amount, 0);
    }
}
