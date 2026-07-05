<?php

namespace App\Jobs;

use App\Models\WebhookEvent;
use App\Services\WebhookProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessProviderWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public int $eventId) { $this->onQueue('webhooks'); }
    public function handle(WebhookProcessor $processor): void { $processor->process(WebhookEvent::query()->findOrFail($this->eventId)); }
}
