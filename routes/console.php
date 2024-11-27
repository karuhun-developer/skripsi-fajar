<?php

use Illuminate\Support\Facades\Schedule;

// Schedule::command('optimize:clear')->daily();

Schedule::command('invoices:generate')
->daily()
->runInBackground();

