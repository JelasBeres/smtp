<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'subject', 'preview_text', 'email_template_id', 'sender_name', 'sender_email', 'reply_to', 'status', 'scheduled_at', 'started_at', 'completed_at', 'created_by', 'total_recipients', 'total_queued', 'total_sent', 'total_delivered', 'total_bounced', 'total_complained', 'total_unsubscribed', 'total_failed'])]
class Campaign extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function template(): BelongsTo { return $this->belongsTo(EmailTemplate::class, 'email_template_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function recipients(): HasMany { return $this->hasMany(CampaignRecipient::class); }
    public function contactLists(): BelongsToMany { return $this->belongsToMany(ContactList::class, 'campaign_contact_lists')->withTimestamps(); }
    public function segments(): BelongsToMany { return $this->belongsToMany(Segment::class, 'campaign_segments')->withTimestamps(); }
}
