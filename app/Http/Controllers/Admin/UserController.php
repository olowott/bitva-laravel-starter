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
use App\Services\ActivityLogService;
use App\Concerns\HandlesTableSorting;
use App\Queries\UserQuery;
use App\Notifications\SystemNotification;

class UserController extends Controller
{
    use HandlesTableSorting;



    public function index(Request $request, UserQuery $userQuery): View
    {

        [$sort, $direction] = $this->resolveTableSort(
            $request,
            [
                'name',
                'email',
                'created_at',
                'is_active',
            ]
        );

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
            ->orderBy($sort, $direction)
            ->orderBy('id', $direction)
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

    public function store(
        StoreUserRequest $request,
        ActivityLogService $activityLogService
    ): RedirectResponse {

        $validated = $request->validated();

        $this->ensureRoleCanBeAssigned(
            $request,
            $validated['role'] ?? null
        );

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


        $user->notify(
            new SystemNotification(
                title: 'Your account has been created',
                message: 'Your account has been created successfully.',
                url: route('profile.edit', absolute: false),
                type: 'success',
                sendEmail: true,
            )
        );


        $activityLogService->log(
            'User created',
            $user,
            [
                'after' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'role' => $user->getRoleNames()->first(),
                ],
            ],
            'created'
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $this->protectSuperAdmin($user);

        $user->load([
            'roles',
            'documents' => fn($query) => $query->latest(),
        ]);

        $roles = $this->availableRoles();

        return view(
            'admin.users.edit',
            compact('user', 'roles')
        );
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        ActivityLogService $activityLogService
    ): RedirectResponse {
        $this->protectSuperAdmin($user);

        $before = [
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
            'role' => $user->getRoleNames()->first(),
        ];

        $validated = $request->validated();

        $newRole = $validated['role']
            ?? $user->getRoleNames()->first();

        $newActiveState = $request->has('is_active')
            ? $request->boolean('is_active')
            : $user->is_active;

        $this->ensureRoleCanBeAssigned(
            $request,
            $newRole
        );

        if (
            $request->user()->is($user)
            && !$newActiveState
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'You cannot deactivate your own account.'
                );
        }

        $this->ensureLastSuperAdminRemains(
            $user,
            $newRole,
            $newActiveState
        );

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }



        $user->is_active = $newActiveState;

        $user->save();

        if (
            $request->user()->can('roles.manage')
            && !empty($validated['role'])
        ) {
            $user->syncRoles([$validated['role']]);
        }

        $user->refresh();

        $after = [
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
            'role' => $user->getRoleNames()->first(),
        ];

        if ($before !== $after) {
            $activityLogService->log(
                'User updated',
                $user,
                [
                    'before' => $before,
                    'after' => $after,
                ],
                'updated'
            );
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(
        Request $request,
        User $user,
        ActivityLogService $activityLogService
    ): RedirectResponse {
        if ($request->user()->is($user)) {
            return back()->with(
                'error',
                'You cannot delete your own account.'
            );
        }

        $this->protectSuperAdmin($user);

        if ($this->isLastSuperAdmin($user)) {
            return back()->with(
                'error',
                'The final super administrator cannot be deleted.'
            );
        }

        $activityLogService->log(
            'User deleted',
            $user,
            [
                'before' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'role' => $user->getRoleNames()->first(),
                ],
            ],
            'deleted'
        );

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

    private function ensureRoleCanBeAssigned(
        Request $request,
        ?string $role
    ): void {
        if (
            $role === 'super_admin'
            && !$request->user()->hasRole('super_admin')
        ) {
            abort(403);
        }
    }

    private function isLastSuperAdmin(User $user): bool
    {
        if (!$user->hasRole('super_admin')) {
            return false;
        }

        return User::role('super_admin')->count() <= 1;
    }

    private function ensureLastSuperAdminRemains(
        User $user,
        ?string $newRole = null,
        ?bool $newActiveState = null
    ): void {
        if (!$this->isLastSuperAdmin($user)) {
            return;
        }

        if (
            $newRole !== null
            && $newRole !== 'super_admin'
        ) {
            abort(
                422,
                'The final super administrator cannot be demoted.'
            );
        }

        if ($newActiveState === false) {
            abort(
                422,
                'The final super administrator cannot be deactivated.'
            );
        }
    }
}
