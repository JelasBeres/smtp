<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['filename', 'source', 'consent_type', 'status', 'imported_count', 'duplicate_count', 'invalid_count', 'failed_count', 'mapping', 'created_by'])]
class ContactImport extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['mapping' => 'array'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
