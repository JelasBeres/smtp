<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_contact_lists', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_list_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['campaign_id', 'contact_list_id']);
        });

        Schema::create('campaign_segments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('segment_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['campaign_id', 'segment_id']);
        });

        Schema::create('unsubscribe_tokens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('token_hash')->unique();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unsubscribe_tokens');
        Schema::dropIfExists('campaign_segments');
        Schema::dropIfExists('campaign_contact_lists');
    }
};
