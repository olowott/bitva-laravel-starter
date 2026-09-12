<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_authorized_user_can_export_activity_logs(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        activity()
            ->causedBy($admin)
            ->event('updated')
            ->log('Application settings updated');

        $response = $this->actingAs($admin)
            ->get(route('admin.activity.export'));

        $response
            ->assertOk()
            ->assertHeader(
                'content-type',
                'text/csv; charset=UTF-8'
            );

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            'Application settings updated',
            $content
        );
    }

    public function test_unauthorized_user_cannot_export_activity_logs(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)
            ->get(route('admin.activity.export'));

        $response->assertForbidden();
    }

    public function test_activity_export_respects_search_filter(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        activity()
            ->causedBy($admin)
            ->event('updated')
            ->log('Application settings updated');

        activity()
            ->causedBy($admin)
            ->event('created')
            ->log('User created');

        $response = $this->actingAs($admin)
            ->get(route('admin.activity.export', [
                'search' => 'settings',
            ]));

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            'Application settings updated',
            $content
        );

        $this->assertStringNotContainsString(
            'User created',
            $content
        );
    }

    public function test_activity_export_respects_event_filter(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        activity()
            ->causedBy($admin)
            ->event('updated')
            ->log('Updated record');

        activity()
            ->causedBy($admin)
            ->event('created')
            ->log('Created record');

        $response = $this->actingAs($admin)
            ->get(route('admin.activity.export', [
                'event' => 'updated',
            ]));

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            'Updated record',
            $content
        );

        $this->assertStringNotContainsString(
            'Created record',
            $content
        );
    }
}
