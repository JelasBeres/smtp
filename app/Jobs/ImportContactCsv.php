<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\ContactImport;
use App\Services\EmailValidationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ImportContactCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(public int $importId, public string $path) { $this->onQueue('imports'); }

    public function backoff(): array { return [30, 120, 300]; }

    public function handle(EmailValidationService $validator): void
    {
        $import = ContactImport::query()->findOrFail($this->importId);
        $import->forceFill(['status' => 'processing'])->save();
        $handle = fopen(Storage::path($this->path), 'r');
        $header = $handle ? fgetcsv($handle) : false;
        if (! $handle || ! $header) { $import->forceFill(['status' => 'failed', 'failed_count' => 1])->save(); return; }

        $mapping = $import->mapping ?: array_combine($header, $header);
        $counts = ['imported_count' => 0, 'duplicate_count' => 0, 'invalid_count' => 0, 'failed_count' => 0];
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row) ?: [];
            $email = $validator->normalize($data[$mapping['email'] ?? 'email'] ?? null);
            $result = $validator->validate($email);
            if (! $email || $result['validation_status'] === Contact::VALIDATION_INVALID_FORMAT) { $counts['invalid_count']++; continue; }
            if (Contact::query()->where('email', $email)->exists()) { $counts['duplicate_count']++; continue; }
            Contact::query()->create([
                'email' => $email,
                'first_name' => trim((string) ($data[$mapping['first_name'] ?? 'first_name'] ?? '')) ?: null,
                'last_name' => trim((string) ($data[$mapping['last_name'] ?? 'last_name'] ?? '')) ?: null,
                'company' => trim((string) ($data[$mapping['company'] ?? 'company'] ?? '')) ?: null,
                'status' => Contact::STATUS_ACTIVE,
                'source' => $import->source,
                'consent_type' => $import->consent_type,
                'consent_at' => now(),
                'validation_status' => $result['validation_status'],
                'risk_level' => $result['risk_level'],
                'subscribed_at' => now(),
            ]);
            $counts['imported_count']++;
        }
        fclose($handle);
        $import->forceFill($counts + ['status' => 'completed'])->save();
    }
}
