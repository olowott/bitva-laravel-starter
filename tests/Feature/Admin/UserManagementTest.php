<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
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

        $superAdmin = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $admin = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $userRole = Role::create([
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

    public function test_super_admin_can_view_users(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);
        $user->assignRole('super_admin');

        $response = $this
            ->actingAs($user)
            ->get(route('admin.users.index'));

        $response->assertOk();
    }

    public function test_admin_can_view_users(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);
        $user->assignRole('admin');

        $response = $this
            ->actingAs($user)
            ->get(route('admin.users.index'));

        $response->assertOk();
    }

    public function test_standard_user_cannot_view_users(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $response = $this
            ->actingAs($user)
            ->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_admin_cannot_delete_users_without_permission(): void
    {
        $admin = User::factory()->create([
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $target = User::factory()->create();
        $target->assignRole('user');

        $response = $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $target));

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
        ]);
    }

    public function test_super_admin_cannot_delete_themselves(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);
        $user->assignRole('super_admin');

        $response = $this
            ->actingAs($user)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors('email');
    }

    public function test_inactive_authenticated_user_is_logged_out(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_user_cannot_deactivate_themselves(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->assignRole('super_admin');

        $response = $this
            ->actingAs($user)
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'super_admin',
                'is_active' => false,
            ]);

        $response->assertRedirect();

        $this->assertTrue(
            $user->fresh()->is_active
        );
    }

    public function test_users_can_be_filtered_by_status(): void
    {
        $admin = User::factory()->create([
            'is_active' => true,
        ]);

        $admin->assignRole('super_admin');

        $activeUser = User::factory()->create([
            'name' => 'Active Person',
            'is_active' => true,
        ]);

        $inactiveUser = User::factory()->create([
            'name' => 'Inactive Person',
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.users.index', [
                'status' => 'active',
            ]));

        $response
            ->assertOk()
            ->assertSee('Active Person')
            ->assertDontSee('Inactive Person');
    }

    public function test_users_can_be_filtered_by_role(): void
    {
        $superAdmin = User::factory()->create([
            'is_active' => true,
        ]);

        $superAdmin->assignRole('super_admin');

        $admin = User::factory()->create([
            'name' => 'Admin Person',
            'is_active' => true,
        ]);

        $admin->assignRole('admin');

        $member = User::factory()->create([
            'name' => 'Regular Person',
            'is_active' => true,
        ]);

        $member->assignRole('user');

        $response = $this
            ->actingAs($superAdmin)
            ->get(route('admin.users.index', [
                'role' => 'admin',
            ]));

        $response
            ->assertOk()
            ->assertSee('Admin Person')
            ->assertDontSee('Regular Person');
    }
}