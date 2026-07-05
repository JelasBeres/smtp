<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Services\CampaignRecipientService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PrepareCampaignRecipients implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public int $campaignId) { $this->onQueue('campaigns'); }
    public function handle(CampaignRecipientService $service): void { $service->prepare(Campaign::query()->findOrFail($this->campaignId)); }
}
