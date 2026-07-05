<?php

namespace App\Contracts;

use App\Models\CampaignRecipient;
use App\Models\EmailProviderSetting;

interface EmailProviderInterface
{
    /** @return array{message_id?: string, status: string} */
    public function send(CampaignRecipient $recipient): array;

    /** @return array{message_id?: string, status: string} */
    public function sendTest(string $email, string $subject, string $html, ?string $text = null): array;

    /** @return array{ok: bool, message: string} */
    public function validateConfiguration(EmailProviderSetting $setting): array;

    /** @return array<string, mixed> */
    public function handleWebhook(array $payload, array $headers = []): array;

    public function getProviderName(): string;
}
