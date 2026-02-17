<?php

Schedule::command('app:fetch-scheduler')->everyMinute()->withoutOverlapping()->runInBackground();
