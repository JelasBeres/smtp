<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['campaign_id', 'contact_id', 'email', 'status', 'provider_message_id', 'queued_at', 'sent_at', 'delivered_at', 'bounced_at', 'complained_at', 'unsubscribed_at', 'failed_at', 'failure_reason'])]
class CampaignRecipient extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_SENT = 'sent';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_BOUNCED = 'bounced';
    public const STATUS_COMPLAINED = 'complained';
    public const STATUS_UNSUBSCRIBED = 'unsubscribed';
    public const STATUS_SUPPRESSED = 'suppressed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SKIPPED = 'skipped';

    protected function casts(): array
    {
        return ['queued_at' => 'datetime', 'sent_at' => 'datetime', 'delivered_at' => 'datetime', 'bounced_at' => 'datetime', 'complained_at' => 'datetime', 'unsubscribed_at' => 'datetime', 'failed_at' => 'datetime'];
    }

    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
}
