<?php

namespace Tests\Feature\Integration;

use App\Models\ContentMultimedia;
use App\Models\ContentProfile;
use App\Models\ContentGalleryImage;
use App\Models\DynamicContent;
use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private NfcToken $token;
    private DynamicContent $dynamicContent;
    private ContentMultimedia $multimedia;
    private ContentProfile $profile;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        $this->user = User::factory()->create();
        
        $this->token = NfcToken::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true
        ]);
        
        $this->dynamicContent = DynamicContent::factory()->create([
            'user_id' => $this->user->id,
            'nfc_token_id' => $this->token->id,
            'type' => 'GIFT',
            'status' => 'published'
        ]);
        
        $this->multimedia = ContentMultimedia::factory()->create([
            'dynamic_content_id' => $this->dynamicContent->id
        ]);
        
        $this->profile = ContentProfile::factory()->create([
            'dynamic_content_id' => $this->dynamicContent->id
        ]);
    }

    /** @test */
    public function complete_gift_content_creation_with_multimedia_uploads()
    {
        $this->actingAs($this->user, 'sanctum');
        
        // Step 1: Create dynamic content
        $dynamicResponse = $this->postJson('/api/content/dynamic', [
            'nfc_token_id' => $this->token->id,
            'type' => 'GIFT',
            'status' => 'draft',
            'title' => 'Test Gift with Multimedia'
        ]);
        
        $dynamicResponse->assertStatus(201);
        $dynamicContentId = $dynamicResponse->json('data.id');
        
        // Step 2: Create gift content
        $giftResponse = $this->postJson("/api/content/gift/{$dynamicContentId}", [
            'recipient_name' => 'John Doe',
            'message' => 'Happy Birthday!',
            'sender_name' => 'Jane Doe',
            'occasion' => 'birthday'
        ]);
        
        $giftResponse->assertStatus(201);
        $giftData = $giftResponse->json('data');
        $multimediaId = $giftData['multimedia']['id'];
        
        // Step 3: Upload audio file
        $audioFile = UploadedFile::fake()->create('birthday_song.mp3', 2000);
        $audioResponse = $this->postJson("/api/content/multimedia/{$multimediaId}/audio", [
            'audio' => $audioFile,
            'type' => 'file'
        ]);
        
        $audioResponse->assertStatus(200);
        $audioUrl = $audioResponse->json('data.audio_url');
        
        // Step 4: Upload video file
        $videoFile = UploadedFile::fake()->create('birthday_message.mp4', 10000);
        $videoResponse = $this->postJson("/api/content/multimedia/{$multimediaId}/video", [
            'video' => $videoFile,
            'type' => 'file'
        ]);
        
        $videoResponse->assertStatus(200);
        $videoUrl = $videoResponse->json('data.video_url');
        
        // Step 5: Upload multiple gallery images
        $galleryImages = [];
        for ($i = 1; $i <= 3; $i++) {
            $imageFile = UploadedFile::fake()->image("birthday_{$i}.jpg", 800, 600);
            $galleryResponse = $this->postJson("/api/content/gallery/{$multimediaId}", [
                'image' => $imageFile,
                'type' => 'file',
                'alt_text' => "Birthday photo {$i}"
            ]);
            
            $galleryResponse->assertStatus(200);
            $galleryImages[] = $galleryResponse->json('data');
        }
        
        // Step 6: Verify all uploads were successful and data is consistent
        
        // Check multimedia record was updated
        $multimedia = ContentMultimedia::find($multimediaId);
        $this->assertEquals('file', $multimedia->audio_type);
        $this->assertEquals('file', $multimedia->video_type);
        $this->assertEquals($audioUrl, $multimedia->audio_url);
        $this->assertEquals($videoUrl, $multimedia->video_url);
        
        // Check gallery images were created
        $this->assertCount(3, ContentGalleryImage::where('multimedia_id', $multimediaId)->get());
        
        // Check files exist in storage
        $this->assertTrue(Storage::disk('public')->exists($audioResponse->json('data.audio_file')));
        $this->assertTrue(Storage::disk('public')->exists($videoResponse->json('data.video_file')));
        
        foreach ($galleryImages as $galleryImage) {
            $this->assertTrue(Storage::disk('public')->exists($galleryImage['image_path']));
        }
        
        // Step 7: Verify token content retrieval includes all uploaded files
        $tokenResponse = $this->getJson("/api/tokens/{$this->token->token_id}");
        $tokenResponse->assertStatus(200);
        
        $tokenData = $tokenResponse->json('data');
        $this->assertArrayHasKey('multimedia', $tokenData['content']);
        $this->assertEquals($audioUrl, $tokenData['content']['multimedia']['audio_url']);
        $this->assertEquals($videoUrl, $tokenData['content']['multimedia']['video_url']);
        $this->assertCount(3, $tokenData['content']['multimedia']['gallery_images']);
    }

    /** @test */
    public function complete_profile_content_creation_with_image_upload()
    {
        $this->actingAs($this->user, 'sanctum');
        
        // Step 1: Create dynamic content for profile
        $dynamicResponse = $this->postJson('/api/content/dynamic', [
            'nfc_token_id' => $this->token->id,
            'type' => 'PROFILE',
            'status' => 'draft',
            'title' => 'Professional Profile'
        ]);
        
        $dynamicResponse->assertStatus(201);
        $dynamicContentId = $dynamicResponse->json('data.id');
        
        // Step 2: Create profile content
        $profileResponse = $this->postJson("/api/content/profile/{$dynamicContentId}", [
            'name' => 'John Professional',
            'bio' => 'Software Engineer with 5 years experience',
            'profession' => 'Software Engineer',
            'company' => 'Tech Corp',
            'contact_email' => 'john@example.com',
            'contact_phone' => '+1234567890'
        ]);
        
        $profileResponse->assertStatus(201);
        $profileId = $profileResponse->json('data.id');
        
        // Step 3: Upload profile image
        $profileImage = UploadedFile::fake()->image('john_profile.jpg', 400, 400);
        $imageResponse = $this->postJson("/api/content/profile/{$profileId}/image", [
            'image' => $profileImage,
            'type' => 'file'
        ]);
        
        $imageResponse->assertStatus(200);
        $imageUrl = $imageResponse->json('data.url');
        
        // Step 4: Verify profile was updated with image
        $profile = ContentProfile::find($profileId);
        $contactInfo = json_decode($profile->contact_info, true);
        $this->assertArrayHasKey('profile_image', $contactInfo);
        $this->assertEquals($imageUrl, $contactInfo['profile_image']);
        
        // Step 5: Verify file exists in storage
        $this->assertTrue(Storage::disk('public')->exists($imageResponse->json('data.file_path')));
        
        // Step 6: Verify token content retrieval includes profile image
        $tokenResponse = $this->getJson("/api/tokens/{$this->token->token_id}");
        $tokenResponse->assertStatus(200);
        
        $tokenData = $tokenResponse->json('data');
        $profileContactInfo = json_decode($tokenData['content']['contact_info'], true);
        $this->assertEquals($imageUrl, $profileContactInfo['profile_image']);
    }

    /** @test */
    public function file_uploads_respect_user_ownership_boundaries()
    {
        $this->actingAs($this->user, 'sanctum');
        
        // Create content for another user
        $otherUser = User::factory()->create();
        $otherToken = NfcToken::factory()->create(['user_id' => $otherUser->id]);
        $otherDynamicContent = DynamicContent::factory()->create([
            'user_id' => $otherUser->id,
            'nfc_token_id' => $otherToken->id,
            'type' => 'GIFT'
        ]);
        $otherMultimedia = ContentMultimedia::factory()->create([
            'dynamic_content_id' => $otherDynamicContent->id
        ]);
        $otherProfile = ContentProfile::factory()->create([
            'dynamic_content_id' => $otherDynamicContent->id
        ]);
        
        $audioFile = UploadedFile::fake()->create('test.mp3', 1000);
        $videoFile = UploadedFile::fake()->create('test.mp4', 1000);
        $imageFile = UploadedFile::fake()->image('test.jpg', 200, 200);
        
        // Attempt to upload to other user's content
        $audioResponse = $this->postJson("/api/content/multimedia/{$otherMultimedia->id}/audio", [
            'audio' => $audioFile,
            'type' => 'file'
        ]);
        $audioResponse->assertStatus(404);
        
        $videoResponse = $this->postJson("/api/content/multimedia/{$otherMultimedia->id}/video", [
            'video' => $videoFile,
            'type' => 'file'
        ]);
        $videoResponse->assertStatus(404);
        
        $profileImageResponse = $this->postJson("/api/content/profile/{$otherProfile->id}/image", [
            'image' => $imageFile,
            'type' => 'file'
        ]);
        $profileImageResponse->assertStatus(404);
        
        $galleryResponse = $this->postJson("/api/content/gallery/{$otherMultimedia->id}", [
            'image' => $imageFile,
            'type' => 'file'
        ]);
        $galleryResponse->assertStatus(404);
        
        // Verify no files were uploaded
        $this->assertEquals(0, Storage::disk('public')->allFiles('audio'));
        $this->assertEquals(0, Storage::disk('public')->allFiles('videos'));
        $this->assertEquals(0, Storage::disk('public')->allFiles('profile-images'));
        $this->assertEquals(0, Storage::disk('public')->allFiles('gallery'));
    }

    /** @test */
    public function multiple_file_uploads_handle_concurrent_requests_properly()
    {
        $this->actingAs($this->user, 'sanctum');
        
        // Create multiple multimedia records
        $multimedia1 = ContentMultimedia::factory()->create([
            'dynamic_content_id' => $this->dynamicContent->id
        ]);
        $multimedia2 = ContentMultimedia::factory()->create([
            'dynamic_content_id' => $this->dynamicContent->id
        ]);
        
        // Upload different files to different multimedia records simultaneously
        $audioFile1 = UploadedFile::fake()->create('audio1.mp3', 1000);
        $audioFile2 = UploadedFile::fake()->create('audio2.mp3', 1000);
        
        $response1 = $this->postJson("/api/content/multimedia/{$multimedia1->id}/audio", [
            'audio' => $audioFile1,
            'type' => 'file'
        ]);
        
        $response2 = $this->postJson("/api/content/multimedia/{$multimedia2->id}/audio", [
            'audio' => $audioFile2,
            'type' => 'file'
        ]);
        
        // Both should succeed
        $response1->assertStatus(200);
        $response2->assertStatus(200);
        
        // Files should have different names
        $file1 = $response1->json('data.audio_file');
        $file2 = $response2->json('data.audio_file');
        $this->assertNotEquals($file1, $file2);
        
        // Both files should exist
        $this->assertTrue(Storage::disk('public')->exists($file1));
        $this->assertTrue(Storage::disk('public')->exists($file2));
        
        // Database records should be updated correctly
        $multimedia1->refresh();
        $multimedia2->refresh();
        $this->assertEquals($response1->json('data.audio_url'), $multimedia1->audio_url);
        $this->assertEquals($response2->json('data.audio_url'), $multimedia2->audio_url);
    }

    /** @test */
    public function file_upload_error_handling_preserves_data_integrity()
    {
        $this->actingAs($this->user, 'sanctum');
        
        $originalAudioUrl = $this->multimedia->audio_url;
        $originalVideoUrl = $this->multimedia->video_url;
        
        // Attempt upload with invalid file type
        $invalidFile = UploadedFile::fake()->create('invalid.txt', 1000);
        
        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/audio", [
            'audio' => $invalidFile,
            'type' => 'file'
        ]);
        
        $response->assertStatus(422);
        
        // Verify original data is unchanged
        $this->multimedia->refresh();
        $this->assertEquals($originalAudioUrl, $this->multimedia->audio_url);
        $this->assertEquals($originalVideoUrl, $this->multimedia->video_url);
        
        // Verify no files were created
        $this->assertEquals(0, Storage::disk('public')->allFiles('audio'));
    }

    /** @test */
    public function file_upload_urls_are_publicly_accessible()
    {
        $this->actingAs($this->user, 'sanctum');
        
        $audioFile = UploadedFile::fake()->create('test.mp3', 1000);
        
        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/audio", [
            'audio' => $audioFile,
            'type' => 'file'
        ]);
        
        $response->assertStatus(200);
        $audioUrl = $response->json('data.audio_url');
        
        // URL should contain asset() generated URL
        $this->assertStringContains(config('app.url'), $audioUrl);
        $this->assertStringContains('storage/', $audioUrl);
        
        // File should be accessible through Laravel's storage URL
        $this->multimedia->refresh();
        $this->assertEquals($audioUrl, $this->multimedia->audio_url);
    }

    /** @test */
    public function gallery_images_maintain_correct_order_and_metadata()
    {
        $this->actingAs($this->user, 'sanctum');
        
        $imageData = [
            ['name' => 'first.jpg', 'alt' => 'First image'],
            ['name' => 'second.jpg', 'alt' => 'Second image'],
            ['name' => 'third.jpg', 'alt' => 'Third image']
        ];
        
        $uploadedImages = [];
        
        foreach ($imageData as $data) {
            $imageFile = UploadedFile::fake()->image($data['name'], 500, 300);
            
            $response = $this->postJson("/api/content/gallery/{$this->multimedia->id}", [
                'image' => $imageFile,
                'type' => 'file',
                'alt_text' => $data['alt']
            ]);
            
            $response->assertStatus(200);
            $uploadedImages[] = $response->json('data');
            
            // Small delay to ensure different timestamps
            usleep(1000);
        }
        
        // Verify all images were created with correct metadata
        $galleryImages = ContentGalleryImage::where('multimedia_id', $this->multimedia->id)
            ->orderBy('created_at')
            ->get();
        
        $this->assertCount(3, $galleryImages);
        
        foreach ($galleryImages as $index => $galleryImage) {
            $this->assertEquals($imageData[$index]['alt'], $galleryImage->alt_text);
            $this->assertEquals('upload', $galleryImage->type);
            $this->assertEquals($this->multimedia->id, $galleryImage->multimedia_id);
            $this->assertTrue(Storage::disk('public')->exists($galleryImage->image_path));
        }
    }
}