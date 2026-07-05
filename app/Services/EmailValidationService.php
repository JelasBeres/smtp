<?php

namespace App\Services;

use App\Models\Contact;

class EmailValidationService
{
    private const DISPOSABLE_DOMAINS = ['mailinator.com', '10minutemail.com', 'tempmail.com', 'guerrillamail.com', 'yopmail.com'];
    private const ROLE_PREFIXES = ['admin', 'info', 'support', 'sales', 'contact', 'noreply', 'no-reply', 'postmaster', 'abuse'];

    /** @return array{email: ?string, validation_status: string, risk_level: string, reasons: array<int, string>} */
    public function validate(?string $email): array
    {
        $normalized = $this->normalize($email);
        $reasons = [];

        if ($normalized === null || ! filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            return ['email' => $normalized, 'validation_status' => Contact::VALIDATION_INVALID_FORMAT, 'risk_level' => Contact::RISK_HIGH, 'reasons' => ['invalid_format']];
        }

        [$local, $domain] = explode('@', $normalized, 2);
        if (! preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/i', $domain)) {
            return ['email' => $normalized, 'validation_status' => Contact::VALIDATION_INVALID_DOMAIN, 'risk_level' => Contact::RISK_HIGH, 'reasons' => ['invalid_domain']];
        }

        $status = Contact::VALIDATION_VALID;
        $risk = Contact::RISK_LOW;

        if (! checkdnsrr($domain, 'MX')) {
            $status = Contact::VALIDATION_NO_MX;
            $risk = Contact::RISK_HIGH;
            $reasons[] = 'no_mx';
        }

        if (in_array($domain, self::DISPOSABLE_DOMAINS, true)) {
            $status = Contact::VALIDATION_DISPOSABLE;
            $risk = Contact::RISK_HIGH;
            $reasons[] = 'disposable';
        }

        if (in_array($local, self::ROLE_PREFIXES, true)) {
            $status = $status === Contact::VALIDATION_VALID ? Contact::VALIDATION_ROLE_BASED : $status;
            $risk = $risk === Contact::RISK_HIGH ? $risk : Contact::RISK_MEDIUM;
            $reasons[] = 'role_based';
        }

        return ['email' => $normalized, 'validation_status' => $status, 'risk_level' => $risk, 'reasons' => $reasons];
    }

    public function normalize(?string $email): ?string
    {
        $email = strtolower(trim((string) $email));
        return $email === '' ? null : $email;
    }
}
