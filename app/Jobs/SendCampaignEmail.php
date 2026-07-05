<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Services\EmailProviderManager;
use App\Services\SuppressionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\RateLimiter;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $timeout = 120;
    public function __construct(public int $recipientId) { $this->onQueue('emails'); }
    public function backoff(): array { return [60, 180, 600, 1800]; }

    public function handle(EmailProviderManager $manager, SuppressionService $suppressions): void
    {
        $recipient = CampaignRecipient::query()->with(['campaign', 'contact'])->findOrFail($this->recipientId);
        if ($recipient->campaign->status === Campaign::STATUS_PAUSED) { $this->release(60); return; }
        if (! $recipient->contact->hasSendableConsent() || $suppressions->isSuppressed($recipient->email)) {
            $recipient->forceFill(['status' => CampaignRecipient::STATUS_SUPPRESSED])->save();
            return;
        }
        if (! RateLimiter::attempt('mailflow-send:minute', 60, fn () => true, 60)) { $this->release(60); return; }
        $result = $manager->active()->send($recipient);
        $recipient->forceFill(['status' => CampaignRecipient::STATUS_SENT, 'sent_at' => now(), 'provider_message_id' => $result['message_id'] ?? null])->save();
        $recipient->campaign->increment('total_sent');
    }
}
