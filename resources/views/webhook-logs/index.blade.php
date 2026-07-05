<x-layouts.app title="Webhook Logs">
    <form method="get" class="mb-5 grid gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-4 md:grid-cols-[1fr_1fr_auto]">
        <input name="provider" value="{{ request('provider') }}" placeholder="Provider" class="rounded-lg border border-slate-800 bg-slate-950 p-3">
        <select name="status" class="rounded-lg border border-slate-800 bg-slate-950 p-3"><option value="">All statuses</option>@foreach(['pending','processed','failed'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select>
        <button class="rounded-lg border border-slate-700 px-4 py-3">Filter</button>
    </form>
    <div class="grid gap-4">
        @forelse($events as $event)
            <section class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between"><div><div class="font-semibold">{{ $event->event_type }}</div><div class="text-sm text-slate-400">{{ $event->provider }} · {{ $event->provider_event_id }}</div></div><span class="rounded-full bg-slate-950 px-3 py-1 text-sm">{{ $event->status }}</span></div>
                @if($event->error_message)<div class="mt-3 rounded border border-red-900 bg-red-950 p-3 text-sm text-red-100">{{ $event->error_message }}</div>@endif
                <details class="mt-3 text-sm"><summary class="cursor-pointer text-slate-300">Payload</summary><pre class="mt-2 overflow-x-auto rounded bg-slate-950 p-3 text-xs text-slate-300">{{ json_encode($event->payload, JSON_PRETTY_PRINT) }}</pre></details>
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-700 p-8 text-center text-slate-400">No webhook events received yet.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $events->links() }}</div>
</x-layouts.app>
