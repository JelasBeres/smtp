<?php

namespace App\Services;

use App\Models\SendingDomain;

class DomainVerificationService
{
    /** @return array{spf_status: string, dkim_status: string, dmarc_status: string, mx_status: string} */
    public function check(string $domain): array
    {
        $domain = strtolower(trim($domain));
        $txt = dns_get_record($domain, DNS_TXT) ?: [];
        $dmarc = dns_get_record('_dmarc.'.$domain, DNS_TXT) ?: [];

        return [
            'spf_status' => $this->containsTxt($txt, 'v=spf1') ? 'valid' : 'warning',
            'dkim_status' => 'pending',
            'dmarc_status' => $this->containsTxt($dmarc, 'v=DMARC1') ? 'valid' : 'warning',
            'mx_status' => checkdnsrr($domain, 'MX') ? 'valid' : 'invalid',
        ];
    }

    public function update(SendingDomain $domain): SendingDomain
    {
        $domain->forceFill($this->check($domain->domain) + ['last_checked_at' => now()])->save();
        return $domain;
    }

    private function containsTxt(array $records, string $needle): bool
    {
        foreach ($records as $record) {
            if (str_contains((string) ($record['txt'] ?? ''), $needle)) {
                return true;
            }
        }

        return false;
    }
}
