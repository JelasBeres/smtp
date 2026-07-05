<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupSuppressionsCommand extends Command
{
    protected $signature = 'suppressions:cleanup';
    protected $description = 'No-op safety command. Suppressions are not deleted automatically.';

    public function handle(): int
    {
        $this->warn('No suppressions removed. Hard bounce and complaint records require manual review.');
        return self::SUCCESS;
    }
}
