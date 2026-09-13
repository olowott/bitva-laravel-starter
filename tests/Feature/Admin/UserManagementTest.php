<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

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

    public function test_users_can_be_searched_by_name_or_email(): void
    {
        $superAdmin = User::factory()->create([
            'is_active' => true,
        ]);

        $superAdmin->assignRole('super_admin');

        User::factory()->create([
            'name' => 'John Example',
            'email' => 'john@example.com',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Mary Example',
            'email' => 'mary@example.com',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->get(route('admin.users.index', [
                'search' => 'john',
            ]));

        $response
            ->assertOk()
            ->assertSee('John Example')
            ->assertDontSee('Mary Example');
    }

    public function test_users_can_be_sorted_by_name_ascending(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        User::factory()->create([
            'name' => 'Zulu User',
        ]);

        User::factory()->create([
            'name' => 'Alpha User',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.index', [
                'sort' => 'name',
                'direction' => 'asc',
            ]));

        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Alpha User',
                'Zulu User',
            ]);
    }

    public function test_users_can_be_sorted_by_name_descending(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        User::factory()->create([
            'name' => 'Alpha User',
        ]);

        User::factory()->create([
            'name' => 'Zulu User',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.index', [
                'sort' => 'name',
                'direction' => 'desc',
            ]));

        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Zulu User',
                'Alpha User',
            ]);
    }

    public function test_admin_cannot_edit_a_super_admin(): void
    {
        $this->seed(
            \Database\Seeders\RolePermissionSeeder::class
        );

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this
            ->actingAs($admin)
            ->get(
                route('admin.users.edit', $superAdmin)
            );

        $response->assertForbidden();
    }

    public function test_admin_cannot_assign_super_admin_role(): void
    {
        $this->seed(
            \Database\Seeders\RolePermissionSeeder::class
        );

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $admin->givePermissionTo([
            'users.create',
            'roles.manage',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(
                route('admin.users.store'),
                [
                    'name' => 'Unauthorized Super Admin',
                    'email' => 'fake-superadmin@example.com',
                    'password' => 'Password123!',
                    'password_confirmation' => 'Password123!',
                    'is_active' => true,
                    'role' => 'super_admin',
                ]
            );

        $response->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => 'fake-superadmin@example.com',
        ]);
    }

    public function test_final_super_admin_cannot_be_demoted(): void
    {
        $this->seed(
            \Database\Seeders\RolePermissionSeeder::class
        );

        $superAdmin = User::factory()->create([
            'is_active' => true,
        ]);

        $superAdmin->assignRole('super_admin');

        $response = $this
            ->actingAs($superAdmin)
            ->put(
                route('admin.users.update', $superAdmin),
                [
                    'name' => $superAdmin->name,
                    'email' => $superAdmin->email,
                    'is_active' => true,
                    'role' => 'user',
                ]
            );

        $response->assertStatus(422);

        $superAdmin->refresh();

        $this->assertTrue(
            $superAdmin->hasRole('super_admin')
        );
    }



    public function test_omitting_active_status_does_not_deactivate_user(): void
    {
        $this->seed(
            \Database\Seeders\RolePermissionSeeder::class
        );

        $superAdmin = User::factory()->create([
            'is_active' => true,
        ]);

        $superAdmin->assignRole('super_admin');

        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->assignRole('user');

        $response = $this
            ->actingAs($superAdmin)
            ->put(
                route('admin.users.update', $user),
                [
                    'name' => 'Updated User',
                    'email' => $user->email,
                    'role' => 'user',

                    // deliberately no is_active
                ]
            );

        $response->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertTrue($user->is_active);
    }

    public function test_new_user_receives_account_created_notification(): void
    {
        Notification::fake();

        $this->seed(
            \Database\Seeders\RolePermissionSeeder::class
        );

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this
            ->actingAs($superAdmin)
            ->post(
                route('admin.users.store'),
                [
                    'name' => 'New User',
                    'email' => 'newuser@example.com',
                    'password' => 'Password123!',
                    'password_confirmation' => 'Password123!',
                    'is_active' => true,
                    'role' => 'user',
                ]
            );

        $response->assertRedirect(
            route('admin.users.index')
        );

        $user = User::where(
            'email',
            'newuser@example.com'
        )->firstOrFail();

        Notification::assertSentTo(
            $user,
            SystemNotification::class,
            function ($notification) use ($user) {
                return $notification->title
                    === 'Your account has been created'
                    && $notification->sendEmail === true
                    && $notification->via($user)
                    === ['database', 'mail'];
            }
        );
    }
}
