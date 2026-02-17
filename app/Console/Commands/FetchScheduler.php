<?php

namespace App\Console\Commands;

use App\Jobs\FetchPricesForUser;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FetchScheduler extends Command
{
    protected $signature = 'app:fetch-scheduler';

    protected $description = 'Checks all users to see if it\'s time to fetch the prices of their products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (User::where('next_fetch', '<=', Carbon::now())->get() as $user) {
            FetchPricesForUser::dispatch($user);
        }
    }
}
