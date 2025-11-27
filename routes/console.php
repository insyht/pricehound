<?php

use App\Jobs\FetchPrices;

Schedule::job(new FetchPrices())->hourly()->withoutOverlapping();
