<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        return view('audit-logs.index', [
            'logs' => AuditLog::query()
                ->with('user')
                ->when($request->filled('action'), fn ($query) => $query->where('action', 'like', '%'.$request->string('action').'%'))
                ->latest()
                ->paginate(30)
                ->withQueryString(),
        ]);
    }
}
