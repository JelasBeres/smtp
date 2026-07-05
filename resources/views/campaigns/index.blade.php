<x-layouts.app title="Campaigns">
    <form method="post" action="{{ route('campaigns.store') }}" class="grid gap-3 rounded-xl border border-slate-800 bg-slate-900 p-5 md:grid-cols-2">
        @csrf
        <input name="name" required placeholder="Campaign name" class="rounded bg-slate-950 p-2">
        <input name="subject" required placeholder="Subject" class="rounded bg-slate-950 p-2">
        <select name="email_template_id" required class="rounded bg-slate-950 p-2"><option value="">Select template</option>@foreach($templates as $template)<option value="{{ $template->id }}">{{ $template->name }}</option>@endforeach</select>
        <input name="sender_name" required placeholder="Sender name" class="rounded bg-slate-950 p-2">
        <input name="sender_email" required placeholder="sender@domain.com" class="rounded bg-slate-950 p-2">
        <input name="reply_to" placeholder="Reply-to" class="rounded bg-slate-950 p-2">
        <button class="rounded bg-blue-600 px-4 py-2">Create draft</button>
    </form>
    <div class="mt-4 overflow-x-auto rounded-xl border border-slate-800 bg-slate-900">
        <table class="w-full text-left text-sm"><thead><tr class="text-slate-400"><th class="p-3">Name</th><th>Status</th><th>Recipients</th><th>Actions</th></tr></thead><tbody>
        @forelse($campaigns as $campaign)
            <tr class="border-t border-slate-800"><td class="p-3">{{ $campaign->name }}</td><td>{{ $campaign->status }}</td><td>{{ $campaign->total_recipients }}</td><td class="flex gap-2 p-3"><form method="post" action="{{ route('campaigns.start', $campaign) }}">@csrf<button class="text-emerald-400">Start</button></form><form method="post" action="{{ route('campaigns.pause', $campaign) }}">@csrf<button>Pause</button></form><form method="post" action="{{ route('campaigns.cancel', $campaign) }}">@csrf<button class="text-red-400">Cancel</button></form></td></tr>
        @empty<tr><td colspan="4" class="p-6 text-slate-400">No campaigns yet.</td></tr>@endforelse
        </tbody></table>
    </div>
</x-layouts.app>
