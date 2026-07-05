<x-layouts.app title="Suppression List">
    <form method="post" action="{{ route('suppressions.store') }}" class="grid gap-3 rounded-xl border border-slate-800 bg-slate-900 p-5 md:grid-cols-4">
        @csrf
        <input name="email" required placeholder="email@example.com" class="rounded bg-slate-950 p-2">
        <select name="reason" class="rounded bg-slate-950 p-2"><option>manual</option><option>hard_bounce</option><option>complaint</option><option>unsubscribe</option><option>invalid</option><option>provider_rejection</option></select>
        <input name="notes" placeholder="Notes" class="rounded bg-slate-950 p-2">
        <button class="rounded bg-blue-600 px-4 py-2">Suppress</button>
    </form>
    <div class="mt-4 grid gap-2">@forelse($suppressions as $s)<div class="rounded border border-slate-800 bg-slate-900 p-3">{{ $s->email }} <span class="text-slate-400">{{ $s->reason }}</span></div>@empty<div class="text-slate-400">No suppressions.</div>@endforelse</div>
</x-layouts.app>
