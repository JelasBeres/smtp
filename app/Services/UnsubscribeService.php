<?php

namespace App\Services;

use App\Models\CampaignRecipient;
use App\Models\Contact;
use App\Models\EmailSuppression;
use App\Models\UnsubscribeToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnsubscribeService
{
    public function createToken(Contact $contact, ?int $campaignId = null): string
    {
        $token = Str::random(64);
        UnsubscribeToken::query()->create(['contact_id' => $contact->id, 'campaign_id' => $campaignId, 'token_hash' => hash('sha256', $token)]);
        return $token;
    }

    public function unsubscribe(string $token): ?Contact
    {
        $row = UnsubscribeToken::query()->where('token_hash', hash('sha256', $token))->first();
        if (! $row) {
            return null;
        }

        return DB::transaction(function () use ($row): Contact {
            $contact = Contact::query()->lockForUpdate()->findOrFail($row->contact_id);
            $contact->forceFill(['status' => Contact::STATUS_UNSUBSCRIBED, 'unsubscribed_at' => now()])->save();
            app(SuppressionService::class)->suppress($contact->email, EmailSuppression::REASON_UNSUBSCRIBE, 'unsubscribe');
            CampaignRecipient::query()->where('contact_id', $contact->id)->whereIn('status', [CampaignRecipient::STATUS_PENDING, CampaignRecipient::STATUS_QUEUED])->update(['status' => CampaignRecipient::STATUS_UNSUBSCRIBED, 'unsubscribed_at' => now()]);
            $row->forceFill(['used_at' => now()])->save();
            return $contact;
        });
    }
}
