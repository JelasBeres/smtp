<?php

namespace App\Http\Controllers;

use App\Models\ContactImport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportHistoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('import-history.index', [
            'imports' => ContactImport::query()
                ->with('creator')
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->latest()
                ->paginate(25)
                ->withQueryString(),
        ]);
    }
}
