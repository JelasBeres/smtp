<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['contact_id', 'campaign_id', 'token_hash', 'used_at'])]
class UnsubscribeToken extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['used_at' => 'datetime'];
    }
}
