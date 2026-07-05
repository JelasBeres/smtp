<?php

namespace App\Services;

use App\Models\CampaignRecipient;
use App\Models\Contact;
use App\Models\EmailSuppression;
use App\Models\WebhookEvent;

class WebhookProcessor
{
    public function process(WebhookEvent $event): void
    {
        if ($event->status === 'processed') {
            return;
        }

        $payload = $event->payload;
        $email = strtolower((string) ($payload['email'] ?? ''));
        $messageId = (string) ($payload['message_id'] ?? $payload['provider_message_id'] ?? '');
        $recipient = $messageId !== '' ? CampaignRecipient::query()->where('provider_message_id', $messageId)->first() : null;
        $status = match ($event->event_type) {
            'delivered' => CampaignRecipient::STATUS_DELIVERED,
            'hard_bounce', 'soft_bounce' => CampaignRecipient::STATUS_BOUNCED,
            'complaint' => CampaignRecipient::STATUS_COMPLAINED,
            'unsubscribe' => CampaignRecipient::STATUS_UNSUBSCRIBED,
            'rejected' => CampaignRecipient::STATUS_FAILED,
            default => null,
        };

        if ($recipient && $status) {
            $recipient->forceFill(['status' => $status, $this->timestampColumn($status) => now()])->save();
        }

        if ($email !== '' && in_array($event->event_type, ['hard_bounce', 'complaint', 'rejected'], true)) {
            $reason = match ($event->event_type) {
                'complaint' => EmailSuppression::REASON_COMPLAINT,
                'rejected' => EmailSuppression::REASON_PROVIDER_REJECTION,
                default => EmailSuppression::REASON_HARD_BOUNCE,
            };
            app(SuppressionService::class)->suppress($email, $reason, 'webhook', $event->provider_event_id);
            Contact::query()->where('email', $email)->update(['status' => $event->event_type === 'complaint' ? Contact::STATUS_COMPLAINED : Contact::STATUS_BOUNCED]);
        }

        $event->forceFill(['status' => 'processed', 'processed_at' => now()])->save();
    }

    private function timestampColumn(string $status): string
    {
        return match ($status) {
            CampaignRecipient::STATUS_DELIVERED => 'delivered_at',
            CampaignRecipient::STATUS_BOUNCED => 'bounced_at',
            CampaignRecipient::STATUS_COMPLAINED => 'complained_at',
            CampaignRecipient::STATUS_UNSUBSCRIBED => 'unsubscribed_at',
            default => 'failed_at',
        };
    }
}
