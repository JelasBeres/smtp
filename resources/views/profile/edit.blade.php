<x-layouts.app title="Profile">
    <form method="POST" action="{{ route('profile.password.update') }}" class="max-w-xl rounded-xl border border-slate-800 bg-slate-900 p-5">
        @csrf
        @method('PUT')
        <h2 class="font-semibold">Change Password</h2>
        <label class="mt-4 block text-sm">Current password</label>
        <input name="current_password" type="password" required class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2">
        <label class="mt-4 block text-sm">New password</label>
        <input name="password" type="password" required class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2">
        <label class="mt-4 block text-sm">Confirm password</label>
        <input name="password_confirmation" type="password" required class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2">
        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-800 bg-red-950 p-3 text-sm text-red-200">{{ $errors->first() }}</div>
        @endif
        <button class="mt-5 rounded-lg bg-cyan-500 px-4 py-2 font-medium text-slate-950 hover:bg-cyan-400">Update password</button>
    </form>
</x-layouts.app>
