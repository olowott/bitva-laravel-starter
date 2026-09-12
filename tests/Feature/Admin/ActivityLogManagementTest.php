<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityLogManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create([
            'name' => 'activity.view',
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

        $admin->givePermissionTo('activity.view');
    }

    public function test_super_admin_can_view_activity_logs(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user)
            ->get(route('admin.activity.index'))
            ->assertOk();
    }

    public function test_admin_with_permission_can_view_activity_logs(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get(route('admin.activity.index'))
            ->assertOk();
    }

    public function test_standard_user_cannot_view_activity_logs(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('admin.activity.index'))
            ->assertForbidden();
    }

    public function test_activity_logs_can_be_filtered_by_event(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        activity()
            ->causedBy($user)
            ->event('created')
            ->log('First activity');

        activity()
            ->causedBy($user)
            ->event('updated')
            ->log('Second activity');

        $response = $this->actingAs($user)
            ->get(route('admin.activity.index', [
                'event' => 'created',
            ]));

        $response
            ->assertOk()
            ->assertSee('First activity')
            ->assertDontSee('Second activity');
    }

    public function test_activity_logs_can_be_searched(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        activity()
            ->causedBy($user)
            ->event('updated')
            ->log('Application settings updated');

        activity()
            ->causedBy($user)
            ->event('created')
            ->log('User created');

        $response = $this->actingAs($user)
            ->get(route('admin.activity.index', [
                'search' => 'settings',
            ]));

        $response
            ->assertOk()
            ->assertSee('Application settings updated')
            ->assertDontSee('User created');
    }

    public function test_sorting_preserves_existing_filters(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        activity()
            ->causedBy($user)
            ->event('updated')
            ->log('Application settings updated');

        $response = $this->actingAs($user)
            ->get(route('admin.activity.index', [
                'search' => 'settings',
                'event' => 'updated',
            ]));

        $response
            ->assertOk()
            ->assertSee('search=settings', false)
            ->assertSee('event=updated', false);
    }

    public function test_activity_logs_can_be_sorted_by_description(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        activity()
            ->causedBy($user)
            ->event('updated')
            ->log('Zulu activity');

        activity()
            ->causedBy($user)
            ->event('created')
            ->log('Alpha activity');

        $response = $this->actingAs($user)
            ->get(route('admin.activity.index', [
                'sort' => 'description',
                'direction' => 'asc',
            ]));

        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Alpha activity',
                'Zulu activity',
            ]);
    }

    public function test_activity_logs_can_be_sorted_by_created_date(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $older = activity()
            ->causedBy($user)
            ->event('created')
            ->log('Older activity');

        $newer = activity()
            ->causedBy($user)
            ->event('updated')
            ->log('Newer activity');

        $older->update([
            'created_at' => now()->subDay(),
        ]);

        $newer->update([
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.activity.index', [
                'sort' => 'created_at',
                'direction' => 'desc',
            ]));

        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Newer activity',
                'Older activity',
            ]);
    }
}
