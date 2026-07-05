<?php

namespace App\Console\Commands;

use App\Models\SendingDomain;
use App\Services\DomainVerificationService;
use Illuminate\Console\Command;

class CheckDomainsCommand extends Command
{
    protected $signature = 'domains:check';
    protected $description = 'Check sending domain DNS records.';

    public function handle(DomainVerificationService $service): int
    {
        SendingDomain::query()->each(fn (SendingDomain $domain) => $service->update($domain));
        $this->info('Domain checks completed.');
        return self::SUCCESS;
    }
}
