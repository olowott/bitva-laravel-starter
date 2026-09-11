<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.view',
            'roles.manage',
            'settings.view',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        $admin->givePermissionTo([
            'users.view',
            'users.create',
            'users.update',
            'roles.view',
            'settings.view',
        ]);
    }

    public function test_super_admin_can_view_roles(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->assignRole('super_admin');

        $this
            ->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertOk();
    }

    public function test_admin_can_view_roles(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->assignRole('admin');

        $this
            ->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertOk();
    }

    public function test_standard_user_cannot_view_roles(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->assignRole('user');

        $this
            ->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_admin_cannot_edit_role_permissions(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->assignRole('admin');

        $role = Role::findByName('user');

        $this
            ->actingAs($user)
            ->get(route('admin.roles.edit', $role))
            ->assertForbidden();
    }

    public function test_super_admin_can_update_role_permissions(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->assignRole('super_admin');

        $role = Role::findByName('admin');

        $response = $this
            ->actingAs($user)
            ->put(route('admin.roles.update', $role), [
                'permissions' => [
                    'users.view',
                    'users.create',
                    'users.update',
                ],
            ]);

        $response->assertRedirect(
            route('admin.roles.index')
        );

        $role->refresh();

        $this->assertTrue(
            $role->hasPermissionTo('users.view')
        );

        $this->assertTrue(
            $role->hasPermissionTo('users.create')
        );

        $this->assertFalse(
            $role->hasPermissionTo('roles.view')
        );
    }

    public function test_super_admin_role_cannot_be_edited(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->assignRole('super_admin');

        $role = Role::findByName('super_admin');

        $this
            ->actingAs($user)
            ->get(route('admin.roles.edit', $role))
            ->assertForbidden();
    }

    public function test_invalid_permission_cannot_be_assigned(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->assignRole('super_admin');

        $role = Role::findByName('admin');

        $response = $this
            ->actingAs($user)
            ->put(route('admin.roles.update', $role), [
                'permissions' => [
                    'users.view',
                    'something.invalid',
                ],
            ]);

        $response->assertSessionHasErrors(
            'permissions.1'
        );
    }
}