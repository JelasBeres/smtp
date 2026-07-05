<?php

namespace App\Services;

use App\Contracts\EmailProviderInterface;
use App\Models\EmailProviderSetting;
use App\Providers\Mail\SesEmailProvider;
use App\Providers\Mail\SmtpEmailProvider;

class EmailProviderManager
{
    public function active(): EmailProviderInterface
    {
        $setting = EmailProviderSetting::query()->where('is_active', true)->first();
        return $this->for($setting?->provider ?? 'smtp');
    }

    public function for(string $provider): EmailProviderInterface
    {
        return match ($provider) {
            'ses' => app(SesEmailProvider::class),
            default => app(SmtpEmailProvider::class),
        };
    }
}
