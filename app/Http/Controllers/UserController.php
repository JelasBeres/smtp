<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        return view('users.index', [
            'users' => User::query()
                ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
                ->when($request->filled('search'), fn ($query) => $query->where(fn ($q) => $q->where('email', 'like', '%'.$request->string('search').'%')->orWhere('name', 'like', '%'.$request->string('search').'%')))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'roles' => $this->roles(),
        ]);
    }

    public function store(Request $request, AuditLogService $audit): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in($this->roles())],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::query()->create($data);
        $audit->record('user.created', $user, newValues: $data, request: $request);

        return back()->with('status', 'User created.');
    }

    public function update(Request $request, User $user, AuditLogService $audit): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in($this->roles())],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $old = $user->only(['name', 'email', 'role']);
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }
        $user->update($data);
        $audit->record('user.updated', $user, $old, $data, $request);

        return back()->with('status', 'User updated.');
    }

    public function destroy(Request $request, User $user, AuditLogService $audit): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $old = $user->only(['name', 'email', 'role']);
        $audit->record('user.deleted', $user, $old, request: $request);
        $user->delete();

        return back()->with('status', 'User deleted.');
    }

    /** @return array<int, string> */
    private function roles(): array
    {
        return [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_OPERATOR, User::ROLE_VIEWER];
    }
}
