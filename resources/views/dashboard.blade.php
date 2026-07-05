<x-layouts.app title="Dashboard">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach (['Active Contacts', 'Suppression Count', 'Running Campaigns', 'Failure Rate'] as $metric)
            <section class="rounded-xl border border-slate-800 bg-slate-900 p-5">
                <div class="text-sm text-slate-400">{{ $metric }}</div>
                <div class="mt-3 text-3xl font-semibold">0</div>
            </section>
        @endforeach
    </div>
    <section class="mt-6 rounded-xl border border-slate-800 bg-slate-900 p-5">
        <h2 class="font-semibold">Tahap 2 Foundation</h2>
        <p class="mt-2 text-sm text-slate-400">Database, consent enforcement, suppression, provider, campaign, and queue structures are being established before email sending features.</p>
    </section>
</x-layouts.app>
