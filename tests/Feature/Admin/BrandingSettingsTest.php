<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BrandingSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            SettingSeeder::class,
        ]);

        Storage::fake('public_assets');
    }

    public function test_authorized_user_can_upload_logo(): void
    {
        $user = User::factory()->create();

        $user->assignRole('super_admin');

        $file = UploadedFile::fake()->image(
            'logo.png',
            400,
            120
        );

        $response = $this
            ->actingAs($user)
            ->put(route('admin.settings.update'), [
                'app_name' => 'BitVa Starter',
                'app_tagline' => 'Starter',
                'company_name' => 'BitVa Tech',
                'company_email' => 'hello@example.com',
                'company_phone' => '12345',
                'timezone' => 'Africa/Lagos',
                'primary_color' => '#4f5bd5',
                'secondary_color' => '#111827',
                'logo' => $file,
            ]);

        $response->assertRedirect();

        $path = setting('logo');

        $this->assertNotNull($path);

        Storage::disk('public_assets')
            ->assertExists($path);
    }

    public function test_uploading_new_logo_replaces_old_logo(): void
    {
        $user = User::factory()->create();

        $user->assignRole('super_admin');

        $oldFile = UploadedFile::fake()->image('old.png');
        $newFile = UploadedFile::fake()->image('new.png');

        $this->actingAs($user)
            ->put(route('admin.settings.update'), [
                'app_name' => 'BitVa Starter',
                'timezone' => 'Africa/Lagos',
                'primary_color' => '#4f5bd5',
                'secondary_color' => '#111827',
                'logo' => $oldFile,
            ]);

        $oldPath = setting('logo');

        $this->actingAs($user)
            ->put(route('admin.settings.update'), [
                'app_name' => 'BitVa Starter',
                'timezone' => 'Africa/Lagos',
                'primary_color' => '#4f5bd5',
                'secondary_color' => '#111827',
                'logo' => $newFile,
            ]);

        $newPath = setting('logo');

        $this->assertNotSame(
            $oldPath,
            $newPath
        );

        Storage::disk('public_assets')
            ->assertMissing($oldPath);

        Storage::disk('public_assets')
            ->assertExists($newPath);
    }

    public function test_authorized_user_can_remove_logo(): void
    {
        $user = User::factory()->create();

        $user->assignRole('super_admin');

        $file = UploadedFile::fake()->image('logo.png');

        Storage::disk('public_assets')
            ->putFileAs(
                'branding',
                $file,
                'logo.png'
            );

        \App\Models\Setting::where(
            'key',
            'logo'
        )->update([
                    'value' => 'branding/logo.png',
                ]);

        app(\App\Services\SettingService::class)
            ->clearCache();

        $response = $this
            ->actingAs($user)
            ->delete(
                route('admin.settings.logo.destroy')
            );

        $response->assertRedirect();

        $this->assertNull(
            setting('logo')
        );

        Storage::disk('public_assets')
            ->assertMissing('branding/logo.png');
    }
}
