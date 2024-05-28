<?php

namespace App\Services\CurrencyConverter;

interface CurrencyApiInterface
{
    public function convert( string $from, string $to, float $amount): float;
}