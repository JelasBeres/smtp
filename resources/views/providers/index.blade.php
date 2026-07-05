<x-layouts.app title="Email Providers">
    <form method="post" action="{{ route('email-providers.store') }}" class="grid gap-3 rounded-xl border border-slate-800 bg-slate-900 p-5 md:grid-cols-3">
        @csrf
        <select name="provider" class="rounded bg-slate-950 p-2"><option value="smtp">SMTP</option><option value="ses">Amazon SES</option></select>
        <input name="name" required placeholder="Name" class="rounded bg-slate-950 p-2"><input name="host" placeholder="SMTP host" class="rounded bg-slate-950 p-2">
        <input name="port" value="587" class="rounded bg-slate-950 p-2"><input name="username" placeholder="Username" class="rounded bg-slate-950 p-2"><input type="password" name="encrypted_password" placeholder="Password" class="rounded bg-slate-950 p-2">
        <input name="from_email" required placeholder="From email" class="rounded bg-slate-950 p-2"><input name="from_name" required placeholder="From name" class="rounded bg-slate-950 p-2"><label><input type="checkbox" name="is_active" value="1"> Active</label>
        <button class="rounded bg-blue-600 px-4 py-2">Save provider</button>
    </form>
    <div class="mt-4 grid gap-2">@foreach($providers as $provider)<div class="rounded border border-slate-800 bg-slate-900 p-3">{{ $provider->name }} <span class="text-slate-400">{{ $provider->provider }} {{ $provider->is_active ? 'active' : '' }}</span></div>@endforeach</div>
</x-layouts.app>
