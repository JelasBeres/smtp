<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Services\EmailValidationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ValidateContactBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public function __construct(public array $contactIds) { $this->onQueue('validation'); }
    public function backoff(): array { return [10, 60, 180]; }

    public function handle(EmailValidationService $service): void
    {
        Contact::query()->whereIn('id', $this->contactIds)->each(function (Contact $contact) use ($service): void {
            $result = $service->validate($contact->email);
            $contact->forceFill(['email' => $result['email'] ?: $contact->email, 'validation_status' => $result['validation_status'], 'risk_level' => $result['risk_level']])->save();
        });
    }
}
