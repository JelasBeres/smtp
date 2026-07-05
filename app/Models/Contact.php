<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['email', 'first_name', 'last_name', 'company', 'phone', 'status', 'source', 'consent_type', 'consent_at', 'consent_ip', 'validation_status', 'risk_level', 'subscribed_at', 'unsubscribed_at', 'last_email_at'])]
class Contact extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_UNSUBSCRIBED = 'unsubscribed';
    public const STATUS_BOUNCED = 'bounced';
    public const STATUS_COMPLAINED = 'complained';
    public const STATUS_SUPPRESSED = 'suppressed';
    public const STATUS_PENDING = 'pending';

    public const VALIDATION_VALID = 'valid';
    public const VALIDATION_INVALID_FORMAT = 'invalid_format';
    public const VALIDATION_INVALID_DOMAIN = 'invalid_domain';
    public const VALIDATION_NO_MX = 'no_mx';
    public const VALIDATION_DISPOSABLE = 'disposable';
    public const VALIDATION_ROLE_BASED = 'role_based';
    public const VALIDATION_RISKY = 'risky';
    public const VALIDATION_UNKNOWN = 'unknown';

    public const RISK_LOW = 'low';
    public const RISK_MEDIUM = 'medium';
    public const RISK_HIGH = 'high';

    protected function casts(): array
    {
        return [
            'consent_at' => 'datetime',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
            'last_email_at' => 'datetime',
        ];
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(ContactList::class, 'contact_list_members')->withTimestamps();
    }

    public function campaignRecipients(): HasMany
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    public function hasSendableConsent(): bool
    {
        return $this->status === self::STATUS_ACTIVE && filled($this->consent_type) && $this->consent_at !== null;
    }
}
