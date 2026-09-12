<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create([
            'name' => 'settings.view',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'settings.manage',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $admin = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        $admin->givePermissionTo([
            'settings.view',
        ]);

        $this->seed(SettingSeeder::class);
    }

    public function test_super_admin_can_view_settings(): void
    {
        $user = User::factory()->create();

        $user->assignRole('super_admin');

        $this
            ->actingAs($user)
            ->get(route('admin.settings.edit'))
            ->assertOk();
    }

    public function test_admin_can_view_settings(): void
    {
        $user = User::factory()->create();

        $user->assignRole('admin');

        $this
            ->actingAs($user)
            ->get(route('admin.settings.edit'))
            ->assertOk();
    }

    public function test_admin_cannot_update_settings_without_permission(): void
    {
        $user = User::factory()->create();

        $user->assignRole('admin');

        $this
            ->actingAs($user)
            ->put(route('admin.settings.update'), [
                'app_name' => 'Changed',
            ])
            ->assertForbidden();
    }

    public function test_super_admin_can_update_settings(): void
    {
        $user = User::factory()->create();

        $user->assignRole('super_admin');

        $response = $this
            ->actingAs($user)
            ->put(route('admin.settings.update'), [
                'app_name' => 'My Application',
                'app_tagline' => 'A better app.',
                'company_name' => 'BitVa Tech',
                'company_email' => 'hello@example.com',
                'company_phone' => '+2348000000000',
                'timezone' => 'Africa/Lagos',
                'primary_color' => '#4f5bd5',
                'secondary_color' => '#111827',
            ]);

        $response->assertRedirect(
            route('admin.settings.edit')
        );

        $this->assertDatabaseHas(
            'settings',
            [
                'key' => 'app_name',
                'value' => 'My Application',
            ]
        );
    }

    public function test_setting_helper_returns_saved_setting(): void
    {
        Setting::query()
            ->where('key', 'app_name')
            ->update([
                'value' => 'Test Application',
            ]);

        app(\App\Services\SettingService::class)
            ->clearCache();

        $this->assertSame(
            'Test Application',
            setting('app_name')
        );
    }
}
