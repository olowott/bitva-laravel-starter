<?php

namespace Tests\Feature\Admin;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\DocumentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\RolePermissionSeeder;

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_document_can_belong_to_a_user(): void
    {
        $user = User::factory()->create();

        $document = Document::create([
            'documentable_type' => $user->getMorphClass(),
            'documentable_id' => $user->id,
            'uploaded_by' => $user->id,
            'category' => 'identity',
            'disk' => 'local',
            'path' => 'documents/test.pdf',
            'original_name' => 'passport.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024,
        ]);

        $this->assertTrue(
            $document->documentable->is($user)
        );

        $this->assertTrue(
            $user->documents->contains($document)
        );
    }

    public function test_authorized_user_can_upload_private_document(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create(
            'passport.pdf',
            500,
            'application/pdf'
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.documents.store'), [
                'user_id' => $user->id,
                'category' => 'identity',
                'document' => $file,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'documentable_type' => $user->getMorphClass(),
            'documentable_id' => $user->id,
            'category' => 'identity',
            'original_name' => 'passport.pdf',
        ]);

        $document = Document::first();

        Storage::disk('local')
            ->assertExists($document->path);
    }

    public function test_authorized_user_can_download_private_document(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create(
            'passport.pdf',
            500,
            'application/pdf'
        );

        $document = app(DocumentService::class)->store(
            file: $file,
            documentable: $user,
            category: 'identity',
        );

        $response = $this->actingAs($admin)
            ->get(route('admin.documents.download', $document));

        $response->assertOk();
    }

    public function test_unauthorized_user_cannot_download_private_document(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $standardUser = User::factory()->create();
        $standardUser->assignRole('user');

        $file = UploadedFile::fake()->create(
            'passport.pdf',
            500,
            'application/pdf'
        );

        $document = app(DocumentService::class)->store(
            file: $file,
            documentable: $admin,
            category: 'identity',
        );

        $response = $this->actingAs($standardUser)
            ->get(route('admin.documents.download', $document));

        $response->assertForbidden();
    }

    public function test_authorized_user_can_delete_document_and_file(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create(
            'passport.pdf',
            500,
            'application/pdf'
        );

        $document = app(DocumentService::class)->store(
            file: $file,
            documentable: $user,
            category: 'identity',
        );

        Storage::disk('local')
            ->assertExists($document->path);

        $response = $this->actingAs($admin)
            ->delete(route('admin.documents.destroy', $document));

        $response->assertRedirect();

        Storage::disk('local')
            ->assertMissing($document->path);

        $this->assertDatabaseMissing('documents', [
            'id' => $document->id,
        ]);
    }

    public function test_invalid_document_type_is_rejected(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create(
            'malicious.exe',
            500,
            'application/octet-stream'
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.documents.store'), [
                'user_id' => $user->id,
                'category' => 'identity',
                'document' => $file,
            ]);

        $response->assertSessionHasErrors('document');

        $this->assertDatabaseCount(
            'documents',
            0
        );
    }

    public function test_document_larger_than_maximum_size_is_rejected(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create(
            'large.pdf',
            10241,
            'application/pdf'
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.documents.store'), [
                'user_id' => $user->id,
                'document' => $file,
            ]);

        $response->assertSessionHasErrors('document');

        $this->assertDatabaseCount(
            'documents',
            0
        );
    }
}
