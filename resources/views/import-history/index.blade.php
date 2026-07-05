<x-layouts.app title="Import History">
    <form method="get" class="mb-5 flex gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-4">
        <select name="status" class="rounded-lg border border-slate-800 bg-slate-950 p-3">
            <option value="">All statuses</option>
            @foreach(['pending','processing','completed','failed'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach
        </select>
        <button class="rounded-lg border border-slate-700 px-4 py-3">Filter</button>
    </form>
    <div class="grid gap-4">
        @forelse($imports as $import)
            <section class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between"><div><div class="font-semibold">{{ $import->filename }}</div><div class="text-sm text-slate-400">{{ $import->source }} · {{ $import->consent_type }} · by {{ $import->creator?->email ?? 'unknown' }}</div></div><span class="rounded-full bg-slate-950 px-3 py-1 text-sm">{{ $import->status }}</span></div>
                <div class="mt-4 grid gap-3 text-sm md:grid-cols-4"><div class="rounded bg-slate-950 p-3">Imported <strong>{{ $import->imported_count }}</strong></div><div class="rounded bg-slate-950 p-3">Duplicate <strong>{{ $import->duplicate_count }}</strong></div><div class="rounded bg-slate-950 p-3">Invalid <strong>{{ $import->invalid_count }}</strong></div><div class="rounded bg-slate-950 p-3">Failed <strong>{{ $import->failed_count }}</strong></div></div>
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-700 p-8 text-center text-slate-400">No CSV imports yet.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $imports->links() }}</div>
</x-layouts.app>
