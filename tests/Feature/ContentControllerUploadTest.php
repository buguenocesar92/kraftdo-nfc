<?php

namespace Tests\Feature;

use App\Models\ContentGalleryImage;
use App\Models\ContentMultimedia;
use App\Models\ContentProfile;
use App\Models\DynamicContent;
use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentControllerUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected NfcToken $token;
    protected DynamicContent $dynamicContent;
    protected ContentProfile $profile;
    protected ContentMultimedia $multimedia;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario de prueba
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        // Crear token NFC
        $this->token = NfcToken::factory()->create([
            'user_id' => $this->user->id,
            'token_id' => 'test-token-123',
            'status' => 'active',
        ]);

        // Crear contenido dinámico
        $this->dynamicContent = DynamicContent::factory()->create([
            'nfc_token_id' => $this->token->id,
            'user_id' => $this->user->id,
            'type' => 'PROFILE',
            'title' => 'Test Profile',
            'is_active' => true,
        ]);

        // Crear perfil de contenido
        $this->profile = ContentProfile::factory()->create([
            'dynamic_content_id' => $this->dynamicContent->id,
            'name' => 'Test User Profile',
        ]);

        // Crear contenido multimedia
        $this->multimedia = ContentMultimedia::factory()->create([
            'dynamic_content_id' => $this->dynamicContent->id,
            'settings' => [],
        ]);

        // Configurar storage falso
        Storage::fake('public');
    }

    /** @test */
    public function authenticated_user_can_upload_profile_image()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('profile.jpg', 800, 600)->size(1000);

        $response = $this->postJson("/api/content/profile/{$this->profile->id}/image", [
            'image' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'url',
                    'file_path',
                ],
                'message',
                'status',
            ])
            ->assertJson([
                'message' => 'Imagen de perfil subida exitosamente',
                'status' => 200,
            ]);

        // Verificar que el archivo se guardó
        $filePath = $response->json('data.file_path');
        Storage::disk('public')->assertExists($filePath);

        // Verificar que se actualizó el multimedia settings
        $this->multimedia->refresh();
        $this->assertArrayHasKey('profile_image', $this->multimedia->settings);
        $this->assertEquals($filePath, $this->multimedia->settings['profile_image']);
    }

    /** @test */
    public function profile_image_upload_validates_file_type()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->postJson("/api/content/profile/{$this->profile->id}/image", [
            'image' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    /** @test */
    public function profile_image_upload_validates_file_size()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('large.jpg')->size(6000); // 6MB > 5MB limit

        $response = $this->postJson("/api/content/profile/{$this->profile->id}/image", [
            'image' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    /** @test */
    public function user_cannot_upload_profile_image_for_other_users_profile()
    {
        $otherUser = User::factory()->create();
        $otherToken = NfcToken::factory()->create(['user_id' => $otherUser->id]);
        $otherDynamicContent = DynamicContent::factory()->create([
            'nfc_token_id' => $otherToken->id,
            'user_id' => $otherUser->id,
        ]);
        $otherProfile = ContentProfile::factory()->create([
            'dynamic_content_id' => $otherDynamicContent->id,
        ]);

        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('profile.jpg');

        $response = $this->postJson("/api/content/profile/{$otherProfile->id}/image", [
            'image' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_upload_gallery_image()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('gallery.jpg', 1200, 800)->size(2000);

        $response = $this->postJson("/api/content/gallery/{$this->multimedia->id}", [
            'image' => $file,
            'alt_text' => 'Test gallery image',
            'type' => 'file_upload',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'image_url',
                    'image_path',
                    'alt_text',
                    'type',
                ],
                'message',
                'status',
            ])
            ->assertJson([
                'message' => 'Imagen subida a galería exitosamente',
                'status' => 200,
                'data' => [
                    'alt_text' => 'Test gallery image',
                    'type' => 'upload',
                ],
            ]);

        // Verificar que el archivo se guardó
        $filePath = $response->json('data.image_path');
        Storage::disk('public')->assertExists($filePath);

        // Verificar que se creó el registro en la base de datos
        $galleryImage = ContentGalleryImage::where('content_multimedia_id', $this->multimedia->id)->first();
        $this->assertNotNull($galleryImage);
        $this->assertEquals('Test gallery image', $galleryImage->alt_text);
        $this->assertEquals($filePath, $galleryImage->image_path);
    }

    /** @test */
    public function gallery_image_gets_default_alt_text_if_not_provided()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('gallery.jpg');

        $response = $this->postJson("/api/content/gallery/{$this->multimedia->id}", [
            'image' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'alt_text' => 'Imagen de galería',
                ],
            ]);
    }

    /** @test */
    public function authenticated_user_can_upload_audio_file()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('audio.mp3', 5000, 'audio/mpeg');

        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/audio", [
            'audio' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'audio_url',
                    'audio_file',
                    'audio_type',
                ],
                'message',
                'status',
            ])
            ->assertJson([
                'message' => 'Archivo de audio subido exitosamente',
                'status' => 200,
                'data' => [
                    'audio_type' => 'file_upload',
                ],
            ]);

        // Verificar que el archivo se guardó
        $filePath = $response->json('data.audio_file');
        Storage::disk('public')->assertExists($filePath);

        // Verificar que se actualizó el multimedia
        $this->multimedia->refresh();
        $this->assertEquals($filePath, $this->multimedia->audio_file);
        $this->assertEquals('file_upload', $this->multimedia->audio_type);
        $this->assertStringContains('storage/' . $filePath, $this->multimedia->audio_url);
    }

    /** @test */
    public function audio_upload_validates_file_type()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/audio", [
            'audio' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['audio']);
    }

    /** @test */
    public function audio_upload_validates_file_size()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('large_audio.mp3', 12000, 'audio/mpeg'); // 12MB > 10MB limit

        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/audio", [
            'audio' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['audio']);
    }

    /** @test */
    public function authenticated_user_can_upload_video_file()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('video.mp4', 25000, 'video/mp4');

        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/video", [
            'video' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'video_url',
                    'video_file',
                    'video_type',
                ],
                'message',
                'status',
            ])
            ->assertJson([
                'message' => 'Archivo de video subido exitosamente',
                'status' => 200,
                'data' => [
                    'video_type' => 'file_upload',
                ],
            ]);

        // Verificar que el archivo se guardó
        $filePath = $response->json('data.video_file');
        Storage::disk('public')->assertExists($filePath);

        // Verificar que se actualizó el multimedia
        $this->multimedia->refresh();
        $this->assertEquals($filePath, $this->multimedia->video_file);
        $this->assertEquals('file_upload', $this->multimedia->video_type);
        $this->assertStringContains('storage/' . $filePath, $this->multimedia->video_url);
    }

    /** @test */
    public function video_upload_validates_file_type()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/video", [
            'video' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['video']);
    }

    /** @test */
    public function video_upload_validates_file_size()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('large_video.mp4', 55000, 'video/mp4'); // 55MB > 50MB limit

        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/video", [
            'video' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['video']);
    }

    /** @test */
    public function user_cannot_upload_to_other_users_multimedia()
    {
        $otherUser = User::factory()->create();
        $otherToken = NfcToken::factory()->create(['user_id' => $otherUser->id]);
        $otherDynamicContent = DynamicContent::factory()->create([
            'nfc_token_id' => $otherToken->id,
            'user_id' => $otherUser->id,
        ]);
        $otherMultimedia = ContentMultimedia::factory()->create([
            'dynamic_content_id' => $otherDynamicContent->id,
        ]);

        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson("/api/content/gallery/{$otherMultimedia->id}", [
            'image' => $file,
            'type' => 'file_upload',
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_delete_gallery_image()
    {
        $this->actingAs($this->user);

        // Crear una imagen en la galería
        $galleryImage = ContentGalleryImage::factory()->create([
            'content_multimedia_id' => $this->multimedia->id,
            'image_path' => 'gallery/test_image.jpg',
        ]);

        // Crear archivo falso para simular que existe
        Storage::disk('public')->put('gallery/test_image.jpg', 'fake image content');

        $response = $this->deleteJson("/api/content/gallery/image/{$galleryImage->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Imagen eliminada exitosamente',
                'status' => 200,
            ]);

        // Verificar que se eliminó de la base de datos
        $this->assertDatabaseMissing('content_gallery_images', [
            'id' => $galleryImage->id,
        ]);

        // Verificar que se eliminó el archivo
        Storage::disk('public')->assertMissing('gallery/test_image.jpg');
    }

    /** @test */
    public function user_cannot_delete_other_users_gallery_image()
    {
        $otherUser = User::factory()->create();
        $otherToken = NfcToken::factory()->create(['user_id' => $otherUser->id]);
        $otherDynamicContent = DynamicContent::factory()->create([
            'nfc_token_id' => $otherToken->id,
            'user_id' => $otherUser->id,
        ]);
        $otherMultimedia = ContentMultimedia::factory()->create([
            'dynamic_content_id' => $otherDynamicContent->id,
        ]);
        $otherGalleryImage = ContentGalleryImage::factory()->create([
            'content_multimedia_id' => $otherMultimedia->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->deleteJson("/api/content/gallery/image/{$otherGalleryImage->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_get_multimedia_content()
    {
        $this->actingAs($this->user);

        // Agregar algunas imágenes a la galería
        ContentGalleryImage::factory()->count(2)->create([
            'content_multimedia_id' => $this->multimedia->id,
        ]);

        $response = $this->getJson("/api/content/multimedia/{$this->dynamicContent->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'dynamic_content_id',
                    'video_url',
                    'audio_url',
                    'settings',
                    'gallery_images' => [
                        '*' => [
                            'id',
                            'content_multimedia_id',
                            'image_path',
                            'image_url',
                            'alt_text',
                        ],
                    ],
                ],
                'message',
                'status',
            ])
            ->assertJson([
                'message' => 'Contenido multimedia obtenido exitosamente',
                'status' => 200,
            ]);

        $this->assertCount(2, $response->json('data.gallery_images'));
    }

    /** @test */
    public function get_multimedia_content_creates_multimedia_if_not_exists()
    {
        $this->actingAs($this->user);

        // Eliminar el multimedia existente
        $this->multimedia->delete();

        $response = $this->getJson("/api/content/multimedia/{$this->dynamicContent->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Contenido multimedia obtenido exitosamente',
                'status' => 200,
            ]);

        // Verificar que se creó un nuevo multimedia
        $newMultimedia = ContentMultimedia::where('dynamic_content_id', $this->dynamicContent->id)->first();
        $this->assertNotNull($newMultimedia);
        $this->assertEquals($this->dynamicContent->id, $newMultimedia->dynamic_content_id);
    }

    /** @test */
    public function user_cannot_access_other_users_multimedia_content()
    {
        $otherUser = User::factory()->create();
        $otherToken = NfcToken::factory()->create(['user_id' => $otherUser->id]);
        $otherDynamicContent = DynamicContent::factory()->create([
            'nfc_token_id' => $otherToken->id,
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->getJson("/api/content/multimedia/{$otherDynamicContent->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function unauthenticated_user_cannot_upload_files()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $endpoints = [
            ['POST', "/api/content/profile/{$this->profile->id}/image"],
            ['POST', "/api/content/gallery/{$this->multimedia->id}"],
            ['POST', "/api/content/multimedia/{$this->multimedia->id}/audio"],
            ['POST', "/api/content/multimedia/{$this->multimedia->id}/video"],
        ];

        foreach ($endpoints as [$method, $endpoint]) {
            $response = $this->json($method, $endpoint, [
                'image' => $file,
                'audio' => $file,
                'video' => $file,
                'type' => 'file_upload',
            ]);

            $response->assertStatus(401);
        }
    }

    /** @test */
    public function file_uploads_require_type_parameter()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson("/api/content/profile/{$this->profile->id}/image", [
            'image' => $file,
            // Missing 'type' parameter
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /** @test */
    public function file_uploads_validate_type_parameter_values()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson("/api/content/profile/{$this->profile->id}/image", [
            'image' => $file,
            'type' => 'invalid_type',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /** @test */
    public function uploaded_files_get_unique_names()
    {
        $this->actingAs($this->user);

        $file1 = UploadedFile::fake()->image('same_name.jpg');
        $file2 = UploadedFile::fake()->image('same_name.jpg');

        // Upload first file
        $response1 = $this->postJson("/api/content/gallery/{$this->multimedia->id}", [
            'image' => $file1,
            'type' => 'file_upload',
        ]);

        // Upload second file with same name
        $response2 = $this->postJson("/api/content/gallery/{$this->multimedia->id}", [
            'image' => $file2,
            'type' => 'file_upload',
        ]);

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        $filePath1 = $response1->json('data.image_path');
        $filePath2 = $response2->json('data.image_path');

        // Files should have different paths due to timestamp prefix
        $this->assertNotEquals($filePath1, $filePath2);
        Storage::disk('public')->assertExists($filePath1);
        Storage::disk('public')->assertExists($filePath2);
    }
}