<?php

namespace App\Console\Commands;

use App\Jobs\ValidateContactBatch;
use App\Models\Contact;
use Illuminate\Console\Command;

class ValidateContactsCommand extends Command
{
    protected $signature = 'contacts:validate {--limit=1000}';
    protected $description = 'Validate pending contacts using safe email checks.';

    public function handle(): int
    {
        Contact::query()->where('validation_status', Contact::VALIDATION_UNKNOWN)->limit((int) $this->option('limit'))->pluck('id')->chunk(100)->each(fn ($ids) => ValidateContactBatch::dispatch($ids->all()));
        $this->info('Validation batches dispatched.');
        return self::SUCCESS;
    }
}
