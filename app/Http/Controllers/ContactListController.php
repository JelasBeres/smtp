<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactListController extends Controller
{
    public function index(): View
    {
        return view('contact-lists.index', [
            'lists' => ContactList::query()
                ->with(['contacts' => fn ($query) => $query->orderBy('email')])
                ->withCount('contacts')
                ->latest()
                ->paginate(20),
            'contacts' => Contact::query()->orderBy('email')->limit(200)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        ContactList::query()->create($data + ['created_by' => $request->user()->id]);

        return back()->with('status', 'Contact list created.');
    }

    public function update(Request $request, ContactList $contactList): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $contactList->update($data);

        return back()->with('status', 'Contact list updated.');
    }

    public function destroy(ContactList $contactList): RedirectResponse
    {
        $contactList->delete();

        return back()->with('status', 'Contact list deleted.');
    }

    public function attachContact(Request $request, ContactList $contactList): RedirectResponse
    {
        $data = $request->validate([
            'contact_id' => ['required', 'exists:contacts,id'],
        ]);

        $contactList->contacts()->syncWithoutDetaching([(int) $data['contact_id']]);

        return back()->with('status', 'Contact added to list.');
    }

    public function detachContact(ContactList $contactList, Contact $contact): RedirectResponse
    {
        $contactList->contacts()->detach($contact->id);

        return back()->with('status', 'Contact removed from list.');
    }
}
