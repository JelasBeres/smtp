<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class CampaignRecipientService
{
    public function prepare(Campaign $campaign): int
    {
        $emails = [];

        $contacts = Contact::query()
            ->where('status', Contact::STATUS_ACTIVE)
            ->whereNotNull('consent_at')
            ->whereNotNull('consent_type')
            ->whereNotExists(fn ($q) => $q->selectRaw('1')->from('email_suppressions')->whereColumn('email_suppressions.email', 'contacts.email'))
            ->get();

        return DB::transaction(function () use ($contacts, $campaign, &$emails): int {
            $count = 0;
            foreach ($contacts as $contact) {
                if (isset($emails[$contact->email])) {
                    continue;
                }
                $emails[$contact->email] = true;
                CampaignRecipient::query()->firstOrCreate(
                    ['campaign_id' => $campaign->id, 'contact_id' => $contact->id],
                    ['email' => $contact->email, 'status' => CampaignRecipient::STATUS_PENDING]
                );
                $count++;
            }
            $campaign->forceFill(['total_recipients' => $campaign->recipients()->count()])->save();
            return $count;
        });
    }
}
