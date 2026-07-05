<x-layouts.app title="Contact Lists">
    <section class="overflow-hidden rounded-2xl border border-slate-800 bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 shadow-2xl shadow-slate-950/40">
        <div class="border-b border-slate-800 p-5">
            <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Create a Permission-Based List</h2>
                    <p class="mt-1 text-sm text-slate-400">Group contacts that already exist in MailFlow. Lists do not create or scrape contacts.</p>
                </div>
                <a href="{{ route('contacts.index') }}" class="rounded-lg border border-slate-700 px-3 py-2 text-sm text-slate-200 hover:border-blue-500 hover:text-white">Manage contacts</a>
            </div>
        </div>
    <form method="post" action="{{ route('contact-lists.store') }}" class="grid gap-3 p-5 md:grid-cols-[1fr_2fr_auto]">
        @csrf
        <label class="grid gap-1 text-sm text-slate-300">
            List name
            <input name="name" required placeholder="Newsletter opt-ins" class="rounded-lg border border-slate-800 bg-slate-950 p-3 text-white outline-none focus:border-blue-500">
        </label>
        <label class="grid gap-1 text-sm text-slate-300">
            Description
            <input name="description" placeholder="Contacts who explicitly subscribed from the website" class="rounded-lg border border-slate-800 bg-slate-950 p-3 text-white outline-none focus:border-blue-500">
        </label>
        <button class="self-end rounded-lg bg-blue-600 px-5 py-3 font-medium text-white hover:bg-blue-500">Create list</button>
    </form>
    </section>

    @if($contacts->isEmpty())
        <div class="mt-4 rounded-2xl border border-amber-700/60 bg-amber-950/40 p-5 text-amber-100">
            <div class="font-semibold">No contacts available yet.</div>
            <p class="mt-1 text-sm text-amber-100/80">Create or import contacts first, then return here to add them into a list.</p>
            <a href="{{ route('contacts.index') }}" class="mt-3 inline-flex rounded-lg bg-amber-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-amber-400">Go to Contacts</a>
        </div>
    @endif

    <div class="mt-6 grid gap-4">
        @forelse($lists as $list)
            @php($attachedIds = $list->contacts->pluck('id')->all())
            <section class="rounded-2xl border border-slate-800 bg-slate-900/90 p-5 shadow-xl shadow-slate-950/30">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <form method="post" action="{{ route('contact-lists.update', $list) }}" class="grid flex-1 gap-3 md:grid-cols-[1fr_1fr_auto]">
                        @csrf
                        @method('put')
                        <label class="grid gap-1 text-xs uppercase tracking-wide text-slate-500">
                            Name
                            <input name="name" value="{{ $list->name }}" required class="rounded-lg border border-slate-800 bg-slate-950 p-3 text-base normal-case tracking-normal text-white outline-none focus:border-blue-500">
                        </label>
                        <label class="grid gap-1 text-xs uppercase tracking-wide text-slate-500">
                            Description
                            <input name="description" value="{{ $list->description }}" class="rounded-lg border border-slate-800 bg-slate-950 p-3 text-base normal-case tracking-normal text-white outline-none focus:border-blue-500">
                        </label>
                        <button class="self-end rounded-lg border border-slate-700 px-4 py-3 text-sm font-medium text-slate-100 hover:border-blue-500 hover:text-white">Save changes</button>
                    </form>
                    <form method="post" action="{{ route('contact-lists.destroy', $list) }}" onsubmit="return confirm('Delete this contact list?')">
                        @csrf
                        @method('delete')
                        <button class="rounded-lg border border-red-900/70 px-4 py-3 text-sm font-medium text-red-300 hover:bg-red-950">Delete</button>
                    </form>
                </div>

                <div class="mt-4 flex flex-wrap gap-2 text-sm">
                    <span class="rounded-full border border-slate-700 bg-slate-950 px-3 py-1 text-slate-300">{{ $list->contacts_count }} members</span>
                    <span class="rounded-full border border-slate-700 bg-slate-950 px-3 py-1 text-slate-400">Created {{ $list->created_at->diffForHumans() }}</span>
                </div>

                <form method="post" action="{{ route('contact-lists.contacts.attach', $list) }}" class="mt-5 grid gap-3 rounded-xl border border-slate-800 bg-slate-950/70 p-4 md:grid-cols-[1fr_auto]">
                    @csrf
                    <select name="contact_id" required @disabled($contacts->isEmpty()) class="rounded-lg border border-slate-800 bg-slate-950 p-3 text-slate-100 outline-none focus:border-emerald-500 disabled:cursor-not-allowed disabled:opacity-50">
                        <option value="">{{ $contacts->isEmpty() ? 'Create contacts first' : 'Choose a contact to add' }}</option>
                        @foreach($contacts as $contact)
                            <option value="{{ $contact->id }}" @disabled(in_array($contact->id, $attachedIds, true))>
                                {{ $contact->email }}{{ in_array($contact->id, $attachedIds, true) ? ' - already in list' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <button @disabled($contacts->isEmpty()) class="rounded-lg bg-emerald-600 px-5 py-3 font-medium text-white hover:bg-emerald-500 disabled:cursor-not-allowed disabled:bg-slate-700 disabled:text-slate-400">Add member</button>
                </form>

                <div class="mt-4 grid gap-2 text-sm">
                    @forelse($list->contacts as $contact)
                        <div class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950 p-3">
                            <div>
                                <div class="font-medium text-slate-100">{{ $contact->email }}</div>
                                <div class="text-xs text-slate-500">{{ $contact->status }} · {{ $contact->consent_type ?: 'no consent type' }}</div>
                            </div>
                            <form method="post" action="{{ route('contact-lists.contacts.detach', [$list, $contact]) }}">
                                @csrf
                                @method('delete')
                                <button class="rounded-lg px-3 py-2 text-red-300 hover:bg-red-950">Remove</button>
                            </form>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-700 bg-slate-950/60 p-5 text-center text-slate-400">
                            This list has no members yet.
                        </div>
                    @endforelse
                </div>
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/70 p-10 text-center">
                <div class="text-lg font-semibold text-white">No contact lists yet</div>
                <p class="mt-2 text-sm text-slate-400">Create your first list above, then attach contacts that already have consent.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $lists->links() }}</div>
</x-layouts.app>
