<?php

 function formatMoney(int $value, string $currency = null): string
{   
    if (!$currency) {
       $currency = config('app.default_currency'); 
    }
    $format = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
    return $format->formatCurrency($value, $currency);
}