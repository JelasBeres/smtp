<?php

namespace App\Services;

use App\Models\Contact;

class TemplateRenderer
{
    public function render(string $content, Contact $contact, string $unsubscribeUrl): string
    {
        $values = [
            '{{first_name}}' => e((string) $contact->first_name),
            '{{last_name}}' => e((string) $contact->last_name),
            '{{email}}' => e($contact->email),
            '{{company}}' => e((string) $contact->company),
            '{{unsubscribe_url}}' => e($unsubscribeUrl),
            '{{current_year}}' => now()->format('Y'),
        ];

        return strtr($content, $values);
    }
}
