<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProviderWebhook;
use App\Models\WebhookEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function store(Request $request, string $provider): JsonResponse
    {
        $payload = $request->all();
        $eventId = (string) ($payload['event_id'] ?? $payload['id'] ?? hash('sha256', $request->getContent()));
        $event = WebhookEvent::query()->firstOrCreate(['provider' => $provider, 'provider_event_id' => $eventId], ['event_type' => (string) ($payload['event_type'] ?? $payload['type'] ?? 'unknown'), 'payload' => $payload, 'status' => 'pending']);
        if ($event->wasRecentlyCreated) { ProcessProviderWebhook::dispatch($event->id); }
        return response()->json(['ok' => true]);
    }
}
