<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueCampaignEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public int $campaignId) { $this->onQueue('campaigns'); }
    public function handle(): void
    {
        $campaign = Campaign::query()->findOrFail($this->campaignId);
        if ($campaign->status === Campaign::STATUS_PAUSED) { $this->release(60); return; }
        $campaign->recipients()->where('status', CampaignRecipient::STATUS_PENDING)->chunkById(100, function ($recipients) use ($campaign): void {
            foreach ($recipients as $recipient) {
                $recipient->forceFill(['status' => CampaignRecipient::STATUS_QUEUED, 'queued_at' => now()])->save();
                SendCampaignEmail::dispatch($recipient->id)->onQueue('emails');
            }
            $campaign->increment('total_queued', $recipients->count());
        });
    }
}
