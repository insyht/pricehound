<?php

namespace App\Enums;

enum HoundEndpoints: string
{
    case Info = 'info';
    case FetchPrice = 'fetch/%s/%';
    case FetchPriceDebug = 'fetch';
}
