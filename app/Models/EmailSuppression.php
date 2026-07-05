<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['email', 'reason', 'source', 'provider_event_id', 'notes'])]
class EmailSuppression extends Model
{
    use HasFactory;

    public const REASON_HARD_BOUNCE = 'hard_bounce';
    public const REASON_COMPLAINT = 'complaint';
    public const REASON_UNSUBSCRIBE = 'unsubscribe';
    public const REASON_MANUAL = 'manual';
    public const REASON_INVALID = 'invalid';
    public const REASON_PROVIDER_REJECTION = 'provider_rejection';
}
