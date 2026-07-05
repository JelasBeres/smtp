<?php

namespace App\Http\Controllers;

use App\Models\EmailSuppression;
use App\Services\SuppressionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuppressionController extends Controller
{
    public function index(Request $request): View
    {
        $suppressions = EmailSuppression::query()->when($request->filled('reason'), fn ($q) => $q->where('reason', $request->string('reason')))->latest()->paginate(25)->withQueryString();
        return view('suppressions.index', compact('suppressions'));
    }
    public function store(Request $request, SuppressionService $service): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email'], 'reason' => ['required'], 'notes' => ['nullable']]);
        $service->suppress($data['email'], $data['reason'], 'manual', notes: $data['notes'] ?? null);
        return back()->with('status', 'Suppression added.');
    }
}
