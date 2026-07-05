<?php

namespace Tests\Feature;

use App\Jobs\ImportContactCsv;
use App\Jobs\SendCampaignEmail;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Contact;
use App\Models\ContactImport;
use App\Models\EmailProviderSetting;
use App\Models\EmailSuppression;
use App\Models\EmailTemplate;
use App\Models\SendingDomain;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Services\CampaignPreflightService;
use App\Services\EmailValidationService;
use App\Services\UnsubscribeService;
use App\Services\WebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MailFlowCoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_validation_normalizes_and_rejects_invalid_format(): void
    {
        $service = app(EmailValidationService::class);

        $this->assertSame('test@example.com', $service->normalize(' Test@Example.COM '));
        $this->assertSame(Contact::VALIDATION_INVALID_FORMAT, $service->validate('not-an-email')['validation_status']);
    }

    public function test_csv_import_deduplicates_email(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $path = 'imports/contacts.csv';
        Storage::put($path, "email,first_name\nA@example.com,A\na@example.com,Duplicate\n");
        $import = ContactImport::query()->create(['filename' => 'contacts.csv', 'source' => 'test', 'consent_type' => 'opt_in', 'created_by' => $user->id, 'mapping' => ['email' => 'email', 'first_name' => 'first_name']]);

        (new ImportContactCsv($import->id, $path))->handle(app(EmailValidationService::class));

        $this->assertSame(1, Contact::query()->count());
        $this->assertSame(1, $import->refresh()->duplicate_count);
    }

    public function test_campaign_recipient_job_blocks_contact_without_consent(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        $template = EmailTemplate::query()->create(['name' => 'T', 'subject' => 'S', 'html_content' => '<p>Hello {{unsubscribe_url}}</p>', 'created_by' => $user->id]);
        $campaign = Campaign::query()->create(['name' => 'C', 'subject' => 'S', 'email_template_id' => $template->id, 'sender_name' => 'Sender', 'sender_email' => 'sender@example.com', 'status' => Campaign::STATUS_PROCESSING, 'created_by' => $user->id]);
        $contact = Contact::query()->create(['email' => 'no-consent@example.com', 'status' => Contact::STATUS_ACTIVE, 'source' => 'test', 'consent_type' => 'opt_in']);
        $recipient = CampaignRecipient::query()->create(['campaign_id' => $campaign->id, 'contact_id' => $contact->id, 'email' => $contact->email, 'status' => CampaignRecipient::STATUS_QUEUED]);

        (new SendCampaignEmail($recipient->id))->handle(app(\App\Services\EmailProviderManager::class), app(\App\Services\SuppressionService::class));

        $this->assertSame(CampaignRecipient::STATUS_SUPPRESSED, $recipient->refresh()->status);
    }

    public function test_unsubscribe_updates_contact_and_suppression(): void
    {
        $contact = Contact::query()->create(['email' => 'person@example.com', 'status' => Contact::STATUS_ACTIVE, 'source' => 'test', 'consent_type' => 'opt_in', 'consent_at' => now()]);
        $token = app(UnsubscribeService::class)->createToken($contact);

        app(UnsubscribeService::class)->unsubscribe($token);

        $this->assertSame(Contact::STATUS_UNSUBSCRIBED, $contact->refresh()->status);
        $this->assertDatabaseHas('email_suppressions', ['email' => 'person@example.com', 'reason' => EmailSuppression::REASON_UNSUBSCRIBE]);
    }

    public function test_webhook_hard_bounce_is_idempotent_and_suppresses(): void
    {
        $event = WebhookEvent::query()->create(['provider' => 'smtp', 'provider_event_id' => 'evt-1', 'event_type' => 'hard_bounce', 'payload' => ['email' => 'bounce@example.com'], 'status' => 'pending']);

        app(WebhookProcessor::class)->process($event);
        app(WebhookProcessor::class)->process($event->refresh());

        $this->assertSame(1, EmailSuppression::query()->where('email', 'bounce@example.com')->count());
        $this->assertSame('processed', $event->refresh()->status);
    }

    public function test_credentials_are_encrypted(): void
    {
        $provider = EmailProviderSetting::query()->create(['provider' => 'smtp', 'name' => 'SMTP', 'host' => 'smtp.example.com', 'port' => 587, 'encrypted_password' => 'secret', 'from_email' => 'sender@example.com', 'from_name' => 'Sender']);

        $this->assertNotSame('secret', $provider->getRawOriginal('encrypted_password'));
        $this->assertSame('secret', $provider->decrypted_password);
    }

    public function test_campaign_preflight_requires_verified_domain_and_active_provider(): void
    {
        $user = User::factory()->create();
        $template = EmailTemplate::query()->create(['name' => 'T', 'subject' => 'S', 'html_content' => '<p>x</p>', 'created_by' => $user->id]);
        $campaign = Campaign::query()->create(['name' => 'C', 'subject' => 'S', 'email_template_id' => $template->id, 'sender_name' => 'Sender', 'sender_email' => 'sender@example.com', 'status' => Campaign::STATUS_DRAFT, 'created_by' => $user->id]);

        $this->assertFalse(app(CampaignPreflightService::class)->check($campaign)['ok']);

        SendingDomain::query()->create(['domain' => 'example.com', 'spf_status' => 'valid', 'dkim_status' => 'valid', 'dmarc_status' => 'valid', 'mx_status' => 'valid', 'provider_verified' => true]);
        EmailProviderSetting::query()->create(['provider' => 'smtp', 'name' => 'SMTP', 'host' => 'smtp.example.com', 'port' => 587, 'from_email' => 'sender@example.com', 'from_name' => 'Sender', 'is_active' => true]);

        $this->assertTrue(app(CampaignPreflightService::class)->check($campaign)['ok']);
    }
}
