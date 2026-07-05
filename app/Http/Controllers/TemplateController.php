<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View { return view('templates.index', ['templates' => EmailTemplate::query()->latest()->paginate(20)]); }
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required'], 'subject' => ['required'], 'preview_text' => ['nullable'], 'html_content' => ['required'], 'text_content' => ['nullable']]);
        EmailTemplate::query()->create($data + ['created_by' => $request->user()->id]);
        return back()->with('status', 'Template saved.');
    }
}
