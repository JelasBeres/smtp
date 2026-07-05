<?php

namespace App\Services;

use App\Models\EmailSuppression;

class SuppressionService
{
    public function isSuppressed(string $email): bool
    {
        return EmailSuppression::query()->where('email', strtolower(trim($email)))->exists();
    }

    public function suppress(string $email, string $reason, string $source, ?string $eventId = null, ?string $notes = null): EmailSuppression
    {
        return EmailSuppression::query()->updateOrCreate(
            ['email' => strtolower(trim($email))],
            ['reason' => $reason, 'source' => $source, 'provider_event_id' => $eventId, 'notes' => $notes]
        );
    }
}
