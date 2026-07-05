<?php

namespace App\Http\Controllers;

use App\Models\EmailProviderSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailProviderController extends Controller
{
    public function index(): View { return view('providers.index', ['providers' => EmailProviderSetting::query()->latest()->paginate(20)]); }
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['provider' => ['required'], 'name' => ['required'], 'host' => ['nullable'], 'port' => ['nullable', 'integer'], 'username' => ['nullable'], 'encrypted_password' => ['nullable'], 'encryption' => ['nullable'], 'api_key' => ['nullable'], 'region' => ['nullable'], 'from_email' => ['required', 'email'], 'from_name' => ['required'], 'reply_to' => ['nullable', 'email'], 'per_minute_limit' => ['nullable', 'integer'], 'hourly_limit' => ['nullable', 'integer'], 'daily_limit' => ['nullable', 'integer'], 'is_active' => ['nullable']]);
        $data['is_active'] = $request->boolean('is_active');
        if ($data['is_active']) { EmailProviderSetting::query()->update(['is_active' => false]); }
        EmailProviderSetting::query()->create($data);
        return back()->with('status', 'Provider saved.');
    }
}
