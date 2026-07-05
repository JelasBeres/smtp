<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

#[Fillable(['provider', 'name', 'host', 'port', 'username', 'encrypted_password', 'encryption', 'api_key', 'region', 'from_email', 'from_name', 'reply_to', 'hourly_limit', 'daily_limit', 'per_minute_limit', 'is_active'])]
class EmailProviderSetting extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function setEncryptedPasswordAttribute(?string $value): void
    {
        $this->attributes['encrypted_password'] = filled($value) ? Crypt::encryptString($value) : null;
    }

    public function getDecryptedPasswordAttribute(): ?string
    {
        return filled($this->encrypted_password) ? Crypt::decryptString($this->encrypted_password) : null;
    }

    public function setApiKeyAttribute(?string $value): void
    {
        $this->attributes['api_key'] = filled($value) ? Crypt::encryptString($value) : null;
    }

    public function getDecryptedApiKeyAttribute(): ?string
    {
        return filled($this->api_key) ? Crypt::decryptString($this->api_key) : null;
    }
}
