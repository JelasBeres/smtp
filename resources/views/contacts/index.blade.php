<x-layouts.app title="Contacts">
    <form method="post" action="{{ route('contacts.store') }}" class="grid gap-3 rounded-xl border border-slate-800 bg-slate-900 p-5 md:grid-cols-3">
        @csrf
        <input name="email" required placeholder="email@example.com" class="rounded bg-slate-950 p-2">
        <input name="first_name" placeholder="First name" class="rounded bg-slate-950 p-2">
        <input name="last_name" placeholder="Last name" class="rounded bg-slate-950 p-2">
        <input name="company" placeholder="Company" class="rounded bg-slate-950 p-2">
        <input name="source" required placeholder="Source" class="rounded bg-slate-950 p-2">
        <input name="consent_type" required placeholder="Consent type" class="rounded bg-slate-950 p-2">
        <button class="rounded bg-blue-600 px-4 py-2">Add contact</button>
    </form>
    <form method="post" action="{{ route('contacts.import') }}" enctype="multipart/form-data" class="mt-4 grid gap-3 rounded-xl border border-slate-800 bg-slate-900 p-5 md:grid-cols-4">
        @csrf
        <input type="file" name="csv" required class="rounded bg-slate-950 p-2">
        <input name="source" required placeholder="Import source" class="rounded bg-slate-950 p-2">
        <input name="consent_type" required placeholder="Consent type" class="rounded bg-slate-950 p-2">
        <button class="rounded bg-emerald-600 px-4 py-2">Queue CSV import</button>
    </form>
    <div class="mt-4 overflow-x-auto rounded-xl border border-slate-800 bg-slate-900">
        <table class="w-full text-left text-sm"><thead><tr class="text-slate-400"><th class="p-3">Email</th><th>Status</th><th>Validation</th><th>Risk</th><th></th></tr></thead><tbody>
        @forelse($contacts as $contact)
            <tr class="border-t border-slate-800"><td class="p-3">{{ $contact->email }}</td><td>{{ $contact->status }}</td><td>{{ $contact->validation_status }}</td><td>{{ $contact->risk_level }}</td><td><form method="post" action="{{ route('contacts.destroy', $contact) }}">@csrf @method('delete')<button class="text-red-400">Delete</button></form></td></tr>
        @empty
            <tr><td colspan="5" class="p-6 text-slate-400">No contacts yet.</td></tr>
        @endforelse
        </tbody></table>
    </div>
    <div class="mt-4">{{ $contacts->links() }}</div>
</x-layouts.app>
