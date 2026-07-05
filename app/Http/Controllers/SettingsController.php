<?php

namespace App\Http\Controllers;

use App\Models\EmailProviderSetting;
use App\Models\SendingDomain;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings.index', [
            'appName' => config('app.name'),
            'environment' => app()->environment(),
            'debug' => config('app.debug'),
            'queue' => config('queue.default'),
            'cache' => config('cache.default'),
            'mailMailer' => config('mail.default'),
            'activeProvider' => EmailProviderSetting::query()->where('is_active', true)->first(),
            'domainCount' => SendingDomain::query()->count(),
        ]);
    }
}
