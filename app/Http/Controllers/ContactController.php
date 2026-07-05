<?php

namespace App\Http\Controllers;

use App\Jobs\ImportContactCsv;
use App\Models\Contact;
use App\Models\ContactImport;
use App\Services\EmailValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $contacts = Contact::query()
            ->when($request->string('search')->isNotEmpty(), fn ($q) => $q->where('email', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()->paginate(25)->withQueryString();
        return view('contacts.index', compact('contacts'));
    }

    public function store(Request $request, EmailValidationService $validator): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email'], 'first_name' => ['nullable', 'string'], 'last_name' => ['nullable', 'string'], 'company' => ['nullable', 'string'], 'source' => ['required', 'string'], 'consent_type' => ['required', 'string']]);
        $result = $validator->validate($data['email']);
        Contact::query()->updateOrCreate(['email' => $result['email']], $data + ['email' => $result['email'], 'status' => Contact::STATUS_ACTIVE, 'consent_at' => now(), 'validation_status' => $result['validation_status'], 'risk_level' => $result['risk_level'], 'subscribed_at' => now()]);
        return back()->with('status', 'Contact saved.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();
        return back()->with('status', 'Contact deleted.');
    }

    public function import(Request $request): RedirectResponse
    {
        $data = $request->validate(['csv' => ['required', 'file', 'mimes:csv,txt', 'max:'.env('MAILFLOW_IMPORT_MAX_KB', 10240)], 'source' => ['required', 'string'], 'consent_type' => ['required', 'string']]);
        $path = $request->file('csv')->store('imports');
        $import = ContactImport::query()->create(['filename' => $request->file('csv')->getClientOriginalName(), 'source' => $data['source'], 'consent_type' => $data['consent_type'], 'created_by' => $request->user()->id, 'mapping' => ['email' => 'email', 'first_name' => 'first_name', 'last_name' => 'last_name', 'company' => 'company']]);
        ImportContactCsv::dispatch($import->id, $path);
        return back()->with('status', 'Import queued.');
    }
}
