<?php

namespace App\Services;

use Dnetix\Redirection\PlacetoPay as RedirectionPlacetoPay;

class PlaceToPay extends RedirectionPlacetoPay
{
    public function __construct()
    {
        parent::__construct([
            'login' => config('services.placetopay.login'),
            'tranKey' => config('services.placetopay.key'),
            'url' => config('services.placetopay.url_base'),
            'rest' => [
                'timeout' => config('services.placetopay.timeout'),
                'connect_timeout' => config('services.placetopay.connect_timeout'),
            ]
        ]);
    }
}
