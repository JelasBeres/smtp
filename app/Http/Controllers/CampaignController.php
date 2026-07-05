<?php

namespace App\Http\Controllers;

use App\Jobs\PrepareCampaignRecipients;
use App\Jobs\QueueCampaignEmails;
use App\Models\Campaign;
use App\Models\EmailTemplate;
use App\Services\CampaignPreflightService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        return view('campaigns.index', ['campaigns' => Campaign::query()->latest()->paginate(20), 'templates' => EmailTemplate::query()->latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required'], 'subject' => ['required'], 'email_template_id' => ['required', 'exists:email_templates,id'], 'sender_name' => ['required'], 'sender_email' => ['required', 'email'], 'reply_to' => ['nullable', 'email'], 'scheduled_at' => ['nullable', 'date']]);
        Campaign::query()->create($data + ['status' => Campaign::STATUS_DRAFT, 'created_by' => $request->user()->id]);
        return back()->with('status', 'Campaign draft created.');
    }

    public function schedule(Request $request, Campaign $campaign): RedirectResponse
    {
        $data = $request->validate(['scheduled_at' => ['required', 'date']]);
        $campaign->forceFill(['scheduled_at' => $data['scheduled_at'], 'status' => Campaign::STATUS_SCHEDULED])->save();
        return back()->with('status', 'Campaign scheduled.');
    }

    public function start(Campaign $campaign, CampaignPreflightService $preflight): RedirectResponse
    {
        $check = $preflight->check($campaign);
        if (! $check['ok']) { return back()->withErrors(['campaign' => implode(' ', $check['errors'])]); }
        $campaign->forceFill(['status' => Campaign::STATUS_PROCESSING, 'started_at' => now()])->save();
        PrepareCampaignRecipients::dispatch($campaign->id)->chain([new QueueCampaignEmails($campaign->id)]);
        return back()->with('status', 'Campaign queued.');
    }

    public function pause(Campaign $campaign): RedirectResponse { $campaign->forceFill(['status' => Campaign::STATUS_PAUSED])->save(); return back()->with('status', 'Campaign paused.'); }
    public function resume(Campaign $campaign): RedirectResponse { $campaign->forceFill(['status' => Campaign::STATUS_PROCESSING])->save(); QueueCampaignEmails::dispatch($campaign->id); return back()->with('status', 'Campaign resumed.'); }
    public function cancel(Campaign $campaign): RedirectResponse { $campaign->forceFill(['status' => Campaign::STATUS_CANCELLED])->save(); return back()->with('status', 'Campaign cancelled.'); }
}
