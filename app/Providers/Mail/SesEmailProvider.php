<?php

namespace App\Providers\Mail;

use App\Contracts\EmailProviderInterface;
use App\Models\CampaignRecipient;
use App\Models\EmailProviderSetting;

class SesEmailProvider implements EmailProviderInterface
{
    public function send(CampaignRecipient $recipient): array
    {
        if (! class_exists(\Aws\SesV2\SesV2Client::class)) {
            throw new \RuntimeException('AWS SDK tidak tersedia.');
        }
        return ['status' => 'queued'];
    }

    public function sendTest(string $email, string $subject, string $html, ?string $text = null): array
    {
        if (! class_exists(\Aws\SesV2\SesV2Client::class)) {
            throw new \RuntimeException('AWS SDK tidak tersedia.');
        }
        return ['status' => 'queued'];
    }

    public function validateConfiguration(EmailProviderSetting $setting): array
    {
        return ['ok' => class_exists(\Aws\SesV2\SesV2Client::class), 'message' => class_exists(\Aws\SesV2\SesV2Client::class) ? 'AWS SDK tersedia.' : 'AWS SDK belum dipasang.'];
    }

    public function handleWebhook(array $payload, array $headers = []): array
    {
        return $payload;
    }

    public function getProviderName(): string
    {
        return 'ses';
    }
}
