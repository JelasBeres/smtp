<?php

namespace App\Console\Commands;

use App\Jobs\PrepareCampaignRecipients;
use App\Jobs\QueueCampaignEmails;
use App\Models\Campaign;
use App\Services\CampaignPreflightService;
use Illuminate\Console\Command;

class DispatchCampaignsCommand extends Command
{
    protected $signature = 'campaigns:dispatch';
    protected $description = 'Dispatch due scheduled campaigns.';

    public function handle(CampaignPreflightService $preflight): int
    {
        Campaign::query()->where('status', Campaign::STATUS_SCHEDULED)->where('scheduled_at', '<=', now())->each(function (Campaign $campaign) use ($preflight): void {
            $check = $preflight->check($campaign);
            if (! $check['ok']) {
                $campaign->forceFill(['status' => Campaign::STATUS_FAILED])->save();
                return;
            }
            $campaign->forceFill(['status' => Campaign::STATUS_PROCESSING, 'started_at' => now()])->save();
            PrepareCampaignRecipients::dispatch($campaign->id)->chain([new QueueCampaignEmails($campaign->id)]);
        });
        $this->info('Due campaigns dispatched.');
        return self::SUCCESS;
    }
}
