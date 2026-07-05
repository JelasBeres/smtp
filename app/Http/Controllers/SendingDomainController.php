<?php

namespace App\Http\Controllers;

use App\Models\SendingDomain;
use App\Services\DomainVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SendingDomainController extends Controller
{
    public function index(): View { return view('domains.index', ['domains' => SendingDomain::query()->latest()->paginate(20)]); }
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['domain' => ['required', 'string']]);
        SendingDomain::query()->firstOrCreate(['domain' => strtolower($data['domain'])]);
        return back()->with('status', 'Domain saved.');
    }
    public function check(SendingDomain $domain, DomainVerificationService $service): RedirectResponse { $service->update($domain); return back()->with('status', 'Domain checked.'); }
}
