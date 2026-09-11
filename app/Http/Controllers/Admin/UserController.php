<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('roles')
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = $request->string('search')->trim();

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            )
            ->when(
                $request->filled('role'),
                fn($query) => $query->role($request->string('role')->toString())
            )
            ->when(
                $request->filled('status'),
                function ($query) use ($request) {
                    if ($request->status === 'active') {
                        $query->where('is_active', true);
                    }

                    if ($request->status === 'inactive') {
                        $query->where('is_active', false);
                    }
                }
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = $this->availableRoles();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active'),
        ]);

        if (
            $request->user()->can('roles.manage')
            && !empty($validated['role'])
        ) {
            $user->assignRole($validated['role']);
        } else {
            $user->assignRole('user');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $this->protectSuperAdmin($user);

        $roles = $this->availableRoles();

        return view(
            'admin.users.edit',
            compact('user', 'roles')
        );
    }

    public function update(
        UpdateUserRequest $request,
        User $user
    ): RedirectResponse {
        $this->protectSuperAdmin($user);

        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (
            $request->user()->is($user)
            && !$request->boolean('is_active')
        ) {
            return back()
                ->withInput()
                ->with('error', 'You cannot deactivate your own account.');
        }

        $user->is_active = $request->boolean('is_active');

        $user->save();

        if (
            $request->user()->can('roles.manage')
            && !empty($validated['role'])
        ) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(
        Request $request,
        User $user
    ): RedirectResponse {
        if ($request->user()->is($user)) {
            return back()->with(
                'error',
                'You cannot delete your own account.'
            );
        }

        $this->protectSuperAdmin($user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    private function protectSuperAdmin(User $user): void
    {
        if (
            $user->hasRole('super_admin')
            && !auth()->user()->hasRole('super_admin')
        ) {
            abort(403);
        }
    }

    private function availableRoles()
    {
        return Role::query()
            ->when(
                !auth()->user()->hasRole('super_admin'),
                fn($query) => $query->where(
                    'name',
                    '!=',
                    'super_admin'
                )
            )
            ->orderBy('name')
            ->get();
    }
}