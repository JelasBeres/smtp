<x-layouts.app title="Audit Logs">
    <form method="get" class="mb-5 grid gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-4 md:grid-cols-[1fr_auto]">
        <input name="action" value="{{ request('action') }}" placeholder="Filter by action, e.g. user.created" class="rounded-lg border border-slate-800 bg-slate-950 p-3">
        <button class="rounded-lg border border-slate-700 px-4 py-3">Filter</button>
    </form>
    <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900">
        <table class="w-full text-left text-sm">
            <thead class="text-slate-400"><tr><th class="p-3">Time</th><th>User</th><th>Action</th><th>Resource</th><th>IP</th></tr></thead>
            <tbody>
            @forelse($logs as $log)
                <tr class="border-t border-slate-800"><td class="p-3 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td><td>{{ $log->user?->email ?? 'system' }}</td><td><span class="rounded bg-slate-950 px-2 py-1">{{ $log->action }}</span></td><td>{{ class_basename($log->auditable_type ?? '') }} #{{ $log->auditable_id }}</td><td>{{ $log->ip_address }}</td></tr>
            @empty
                <tr><td colspan="5" class="p-8 text-center text-slate-400">No audit logs yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</x-layouts.app>
