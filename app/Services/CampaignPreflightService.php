<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\EmailProviderSetting;
use App\Models\SendingDomain;

class CampaignPreflightService
{
    /** @return array{ok: bool, errors: array<int, string>} */
    public function check(Campaign $campaign): array
    {
        $errors = [];

        if (! filter_var($campaign->sender_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Sender email tidak valid.';
        }

        $domainName = substr(strrchr($campaign->sender_email, '@') ?: '', 1);
        $domain = $domainName ? SendingDomain::query()->where('domain', $domainName)->first() : null;
        if (! $domain || ! $domain->provider_verified) {
            $errors[] = 'Domain pengirim belum terverifikasi provider.';
        }
        if ($domain && in_array('invalid', [$domain->spf_status, $domain->dkim_status, $domain->dmarc_status, $domain->mx_status], true)) {
            $errors[] = 'Status DNS domain belum memenuhi syarat.';
        }
        if (! EmailProviderSetting::query()->where('is_active', true)->exists()) {
            $errors[] = 'Tidak ada provider email aktif.';
        }
        if (! $campaign->template()->exists()) {
            $errors[] = 'Template tidak tersedia.';
        }

        return ['ok' => $errors === [], 'errors' => $errors];
    }
}
