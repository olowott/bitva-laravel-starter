<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_super_admin_cannot_delete_their_account(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('super_admin');

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    public function test_profile_information_can_be_updated_with_extended_fields(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Tope Johnson',
                'email' => $user->email,
                'phone' => '08012345678',
                'job_title' => 'Laravel Developer',
                'bio' => 'Building reusable Laravel applications.',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Tope Johnson', $user->name);
        $this->assertSame('08012345678', $user->phone);
        $this->assertSame('Laravel Developer', $user->job_title);
        $this->assertSame(
            'Building reusable Laravel applications.',
            $user->bio
        );
    }

    public function test_user_can_upload_an_avatar(): void
    {
        Storage::fake('public_assets');

        $user = User::factory()->create();

        $avatar = UploadedFile::fake()->image('avatar.jpg');

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $avatar,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertNotNull($user->avatar);

        Storage::disk('public_assets')
            ->assertExists($user->avatar);
    }

    public function test_uploading_a_new_avatar_deletes_the_old_avatar(): void
    {
        Storage::fake('public_assets');

        $user = User::factory()->create([
            'avatar' => 'avatars/old-avatar.jpg',
        ]);

        Storage::disk('public_assets')
            ->put('avatars/old-avatar.jpg', 'old avatar');

        $avatar = UploadedFile::fake()->image('new-avatar.jpg');

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $avatar,
            ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();

        Storage::disk('public_assets')
            ->assertMissing('avatars/old-avatar.jpg');

        Storage::disk('public_assets')
            ->assertExists($user->avatar);
    }

    public function test_user_can_remove_their_avatar(): void
    {
        Storage::fake('public_assets');

        $user = User::factory()->create([
            'avatar' => 'avatars/avatar.jpg',
        ]);

        Storage::disk('public_assets')
            ->put('avatars/avatar.jpg', 'avatar');

        $response = $this
            ->actingAs($user)
            ->delete('/profile/avatar');

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user->refresh();

        $this->assertNull($user->avatar);

        Storage::disk('public_assets')
            ->assertMissing('avatars/avatar.jpg');
    }

    public function test_avatar_must_be_a_valid_image(): void
    {
        Storage::fake('public_assets');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create(
            'document.pdf',
            100,
            'application/pdf'
        );

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $file,
            ]);

        $response->assertSessionHasErrors('avatar');
    }
}
