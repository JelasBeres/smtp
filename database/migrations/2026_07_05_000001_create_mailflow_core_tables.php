<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('status')->default('pending')->index();
            $table->string('source')->index();
            $table->string('consent_type')->index();
            $table->timestamp('consent_at')->nullable();
            $table->string('consent_ip', 45)->nullable();
            $table->string('validation_status')->default('unknown')->index();
            $table->string('risk_level')->default('medium')->index();
            $table->timestamp('subscribed_at')->nullable()->index();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('last_email_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('contact_lists', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('contact_list_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contact_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['contact_list_id', 'contact_id']);
        });

        Schema::create('segments', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('rules');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('email_suppressions', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->unique();
            $table->string('reason')->index();
            $table->string('source')->index();
            $table->string('provider_event_id')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('email_provider_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('provider')->index();
            $table->string('name');
            $table->string('host')->nullable();
            $table->unsignedInteger('port')->nullable();
            $table->string('username')->nullable();
            $table->text('encrypted_password')->nullable();
            $table->string('encryption')->nullable();
            $table->text('api_key')->nullable();
            $table->string('region')->nullable();
            $table->string('from_email');
            $table->string('from_name');
            $table->string('reply_to')->nullable();
            $table->unsignedInteger('hourly_limit')->nullable();
            $table->unsignedInteger('daily_limit')->nullable();
            $table->unsignedInteger('per_minute_limit')->nullable();
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('sending_domains', function (Blueprint $table): void {
            $table->id();
            $table->string('domain')->unique();
            $table->string('mail_from_subdomain')->nullable();
            $table->string('tracking_subdomain')->nullable();
            $table->string('spf_status')->default('pending');
            $table->string('dkim_status')->default('pending');
            $table->string('dmarc_status')->default('pending');
            $table->string('mx_status')->default('pending');
            $table->boolean('provider_verified')->default(false)->index();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('email_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->string('preview_text')->nullable();
            $table->longText('html_content');
            $table->longText('text_content')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->string('preview_text')->nullable();
            $table->foreignId('email_template_id')->constrained()->restrictOnDelete();
            $table->string('sender_name');
            $table->string('sender_email');
            $table->string('reply_to')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('total_queued')->default(0);
            $table->unsignedInteger('total_sent')->default(0);
            $table->unsignedInteger('total_delivered')->default(0);
            $table->unsignedInteger('total_bounced')->default(0);
            $table->unsignedInteger('total_complained')->default(0);
            $table->unsignedInteger('total_unsubscribed')->default(0);
            $table->unsignedInteger('total_failed')->default(0);
            $table->timestamps();
        });

        Schema::create('campaign_recipients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->restrictOnDelete();
            $table->string('email')->index();
            $table->string('status')->default('pending')->index();
            $table->string('provider_message_id')->nullable()->index();
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('complained_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            $table->unique(['campaign_id', 'contact_id']);
        });

        Schema::create('webhook_events', function (Blueprint $table): void {
            $table->id();
            $table->string('provider');
            $table->string('provider_event_id');
            $table->string('event_type')->index();
            $table->json('payload');
            $table->string('status')->default('pending')->index();
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'provider_event_id']);
        });

        Schema::create('contact_imports', function (Blueprint $table): void {
            $table->id();
            $table->string('filename');
            $table->string('source');
            $table->string('consent_type');
            $table->string('status')->default('pending')->index();
            $table->unsignedInteger('imported_count')->default(0);
            $table->unsignedInteger('duplicate_count')->default(0);
            $table->unsignedInteger('invalid_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->json('mapping')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action')->index();
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('contact_imports');
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('campaign_recipients');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('sending_domains');
        Schema::dropIfExists('email_provider_settings');
        Schema::dropIfExists('email_suppressions');
        Schema::dropIfExists('segments');
        Schema::dropIfExists('contact_list_members');
        Schema::dropIfExists('contact_lists');
        Schema::dropIfExists('contacts');
    }
};
