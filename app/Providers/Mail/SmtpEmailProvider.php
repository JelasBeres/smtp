<?php

namespace App\Providers\Mail;

use App\Contracts\EmailProviderInterface;
use App\Models\CampaignRecipient;
use App\Models\EmailProviderSetting;
use App\Services\TemplateRenderer;
use App\Services\UnsubscribeService;
use Illuminate\Support\Facades\Mail;

class SmtpEmailProvider implements EmailProviderInterface
{
    public function send(CampaignRecipient $recipient): array
    {
        $campaign = $recipient->campaign()->with('template')->firstOrFail();
        $token = app(UnsubscribeService::class)->createToken($recipient->contact, $campaign->id);
        $unsubscribeUrl = route('unsubscribe.show', $token);
        $html = app(TemplateRenderer::class)->render($campaign->template->html_content, $recipient->contact, $unsubscribeUrl);
        $text = $campaign->template->text_content ? app(TemplateRenderer::class)->render($campaign->template->text_content, $recipient->contact, $unsubscribeUrl) : strip_tags($html);

        Mail::html($html, function ($message) use ($campaign, $recipient, $unsubscribeUrl): void {
            $message->to($recipient->email)
                ->from($campaign->sender_email, $campaign->sender_name)
                ->subject($campaign->subject)
                ->replyTo($campaign->reply_to ?: $campaign->sender_email)
                ->getHeaders()->addTextHeader('List-Unsubscribe', '<'.$unsubscribeUrl.'>');
            $message->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        });

        return ['status' => 'sent'];
    }

    public function sendTest(string $email, string $subject, string $html, ?string $text = null): array
    {
        Mail::html($html, fn ($message) => $message->to($email)->subject($subject));
        return ['status' => 'sent'];
    }

    public function validateConfiguration(EmailProviderSetting $setting): array
    {
        if ($setting->provider !== 'smtp') {
            return ['ok' => false, 'message' => 'Provider bukan SMTP.'];
        }
        if (! in_array((int) $setting->port, [25, 465, 587], true)) {
            return ['ok' => false, 'message' => 'Port SMTP tidak didukung.'];
        }
        return ['ok' => filled($setting->host) && filled($setting->from_email), 'message' => 'Konfigurasi SMTP terlihat valid.'];
    }

    public function handleWebhook(array $payload, array $headers = []): array
    {
        return $payload;
    }

    public function getProviderName(): string
    {
        return 'smtp';
    }
}
