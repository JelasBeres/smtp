<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['domain', 'mail_from_subdomain', 'tracking_subdomain', 'spf_status', 'dkim_status', 'dmarc_status', 'mx_status', 'provider_verified', 'last_checked_at'])]
class SendingDomain extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['provider_verified' => 'boolean', 'last_checked_at' => 'datetime'];
    }
}
