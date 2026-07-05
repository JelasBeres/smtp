<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogService
{
    private const SENSITIVE_KEYS = ['password', 'password_confirmation', 'api_key', 'encrypted_password', 'smtp_password', 'secret', 'token'];

    /** @param array<string, mixed> $oldValues @param array<string, mixed> $newValues */
    public function record(string $action, ?Model $model = null, array $oldValues = [], array $newValues = [], ?Request $request = null): AuditLog
    {
        $request ??= request();

        return AuditLog::query()->create([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'auditable_type' => $model?->getMorphClass(),
            'auditable_id' => $model?->getKey(),
            'old_values' => $this->sanitize($oldValues),
            'new_values' => $this->sanitize($newValues),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /** @param array<string, mixed> $values @return array<string, mixed> */
    private function sanitize(array $values): array
    {
        foreach ($values as $key => $value) {
            if (in_array(strtolower((string) $key), self::SENSITIVE_KEYS, true)) {
                $values[$key] = '[redacted]';
            }
        }

        return $values;
    }
}
