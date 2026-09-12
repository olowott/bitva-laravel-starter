<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_authorized_user_can_export_users_as_csv(): void
    {
        $admin = User::factory()->create([
            'name' => 'Export Admin',
            'email' => 'admin@example.com',
        ]);

        $admin->assignRole('super_admin');

        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.export'));

        $response
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            'John Doe',
            $content
        );

        $this->assertStringContainsString(
            'john@example.com',
            $content
        );
    }

    public function test_unauthorized_user_cannot_export_users(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)
            ->get(route('admin.users.export'));

        $response->assertForbidden();
    }

    public function test_user_export_respects_search_filter(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.export', [
                'search' => 'John',
            ]));

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            'John Doe',
            $content
        );

        $this->assertStringNotContainsString(
            'Jane Smith',
            $content
        );
    }

    public function test_user_export_respects_status_filter(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        User::factory()->create([
            'name' => 'Active User',
            'email' => 'active@example.com',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.export', [
                'status' => 'active',
            ]));

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            'Active User',
            $content
        );

        $this->assertStringNotContainsString(
            'Inactive User',
            $content
        );
    }

    public function test_user_export_respects_role_filter(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $adminUser = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'system@example.com',
        ]);

        $adminUser->assignRole('admin');

        $standardUser = User::factory()->create([
            'name' => 'Standard User',
            'email' => 'standard@example.com',
        ]);

        $standardUser->assignRole('user');

        $response = $this->actingAs($admin)
            ->get(route('admin.users.export', [
                'role' => 'admin',
            ]));

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            'System Admin',
            $content
        );

        $this->assertStringNotContainsString(
            'Standard User',
            $content
        );
    }

    public function test_user_export_contains_expected_csv_headings(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)
            ->get(route('admin.users.export'));

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            'Name,Email,Role,Status,"Created At"',
            $content
        );
    }
}
