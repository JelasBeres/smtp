<?php

namespace App\Http\Controllers;

use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebhookLogController extends Controller
{
    public function index(Request $request): View
    {
        return view('webhook-logs.index', [
            'events' => WebhookEvent::query()
                ->when($request->filled('provider'), fn ($query) => $query->where('provider', $request->string('provider')))
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->latest()
                ->paginate(25)
                ->withQueryString(),
        ]);
    }
}
