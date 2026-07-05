<x-layouts.app title="Users">
    <section class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
        <div class="mb-4">
            <h2 class="text-lg font-semibold">Create Admin User</h2>
            <p class="text-sm text-slate-400">Only super admins can manage users. Passwords are hashed and never shown again.</p>
        </div>
        <form method="post" action="{{ route('users.store') }}" class="grid gap-3 md:grid-cols-5">
            @csrf
            <input name="name" required placeholder="Name" class="rounded-lg border border-slate-800 bg-slate-950 p-3">
            <input name="email" required type="email" placeholder="Email" class="rounded-lg border border-slate-800 bg-slate-950 p-3">
            <select name="role" required class="rounded-lg border border-slate-800 bg-slate-950 p-3">
                @foreach($roles as $role)<option value="{{ $role }}">{{ $role }}</option>@endforeach
            </select>
            <input name="password" required type="password" minlength="8" placeholder="Temporary password" class="rounded-lg border border-slate-800 bg-slate-950 p-3">
            <button class="rounded-lg bg-blue-600 px-4 py-3 font-medium hover:bg-blue-500">Create</button>
        </form>
    </section>

    <form method="get" class="mt-5 grid gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-4 md:grid-cols-[1fr_auto_auto]">
        <input name="search" value="{{ request('search') }}" placeholder="Search name or email" class="rounded-lg border border-slate-800 bg-slate-950 p-3">
        <select name="role" class="rounded-lg border border-slate-800 bg-slate-950 p-3">
            <option value="">All roles</option>
            @foreach($roles as $role)<option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>@endforeach
        </select>
        <button class="rounded-lg border border-slate-700 px-4 py-3">Filter</button>
    </form>

    <div class="mt-5 grid gap-4">
        @forelse($users as $user)
            <section class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                <form method="post" action="{{ route('users.update', $user) }}" class="grid gap-3 lg:grid-cols-[1fr_1fr_auto_1fr_auto]">
                    @csrf
                    @method('put')
                    <input name="name" value="{{ $user->name }}" required class="rounded-lg border border-slate-800 bg-slate-950 p-3">
                    <input name="email" value="{{ $user->email }}" type="email" required class="rounded-lg border border-slate-800 bg-slate-950 p-3">
                    <select name="role" required class="rounded-lg border border-slate-800 bg-slate-950 p-3">
                        @foreach($roles as $role)<option value="{{ $role }}" @selected($user->role === $role)>{{ $role }}</option>@endforeach
                    </select>
                    <input name="password" type="password" minlength="8" placeholder="New password optional" class="rounded-lg border border-slate-800 bg-slate-950 p-3">
                    <button class="rounded-lg border border-slate-700 px-4 py-3 hover:border-blue-500">Save</button>
                </form>
                <div class="mt-3 flex items-center justify-between text-sm text-slate-400">
                    <span>Created {{ $user->created_at->diffForHumans() }}</span>
                    @if(! auth()->user()->is($user))
                        <form method="post" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                            @csrf
                            @method('delete')
                            <button class="text-red-300 hover:text-red-200">Delete</button>
                        </form>
                    @else
                        <span class="text-slate-500">Current user</span>
                    @endif
                </div>
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-700 p-8 text-center text-slate-400">No users found.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
</x-layouts.app>
