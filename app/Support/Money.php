<?php

namespace App\Support;

class Money
{
    public static function format($amount): string
    {
        return 'Rs. '.number_format((float) $amount, 2);
    }

    public static function qty($quantity): string
    {
        // Drop trailing zeros so whole quantities read as "10", not "10.00".
        return rtrim(rtrim(number_format((float) $quantity, 3), '0'), '.');
    }
}
