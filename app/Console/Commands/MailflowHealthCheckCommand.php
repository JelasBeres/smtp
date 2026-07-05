<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MailflowHealthCheckCommand extends Command
{
    protected $signature = 'mailflow:health-check';
    protected $description = 'Check database and cache connectivity.';

    public function handle(): int
    {
        DB::select('select 1');
        Cache::put('mailflow-health', now()->toIso8601String(), 60);
        $this->info('MailFlow health check passed.');
        return self::SUCCESS;
    }
}
