<?php

namespace App\Enums;

enum HoundEndpoints: string
{
    case Ping = 'ping';
    case Profile = 'user';
    case FetchPricesForProducts = 'fetch/%s'; // string of an imploded (comma-separated) array of identifiers
}
