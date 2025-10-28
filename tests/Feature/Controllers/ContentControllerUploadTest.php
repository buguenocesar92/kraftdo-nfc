<?php

use App\Models\ContentMultimedia;
use App\Models\ContentProfile;
use App\Models\ContentGalleryImage;
use App\Models\DynamicContent;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    
    $this->user = User::factory()->create();
    
    $this->dynamicContent = DynamicContent::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'GIFT'
    ]);
    
    $this->multimedia = ContentMultimedia::factory()->create([
        'dynamic_content_id' => $this->dynamicContent->id
    ]);
    
    $this->profile = ContentProfile::factory()->create([
        'dynamic_content_id' => $this->dynamicContent->id
    ]);
    
    $this->actingAs($this->user);
});

describe('Upload Audio File', function () {
    it('validates required fields', function () {
        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/audio", []);
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['audio', 'type']);
    });

    it('validates file type', function () {
        $invalidFile = UploadedFile::fake()->create('test.txt', 100, 'text/plain');
        
        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/audio", [
            'type' => 'file_upload',
            'audio' => $invalidFile
        ]);
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['audio']);
    });

    it('validates file size', function () {
        $largeFile = UploadedFile::fake()->create('test.mp3', 11000, 'audio/mp3'); // 11MB > 10MB limit
        
        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/audio", [
            'type' => 'file_upload',
            'audio' => $largeFile
        ]);
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['audio']);
    });

    it('succeeds with valid file', function () {
        $audioFile = UploadedFile::fake()->create('test.mp3', 1000, 'audio/mp3');
        
        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/audio", [
            'type' => 'file_upload',
            'audio' => $audioFile
        ]);
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'audio_url',
                         'audio_file',
                         'audio_type'
                     ]
                 ]);
        
        expect($response->json('data.audio_type'))->toBe('file_upload');
        
        // Verify file was stored
        $filePath = $response->json('data.audio_file');
        expect(Storage::disk('public')->exists($filePath))->toBeTrue();
        
        // Verify database was updated
        $this->multimedia->refresh();
        expect($this->multimedia->audio_type)->toBe('file_upload');
        expect($this->multimedia->audio_file)->not->toBeNull();
    });

    it('fails for non-owner', function () {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);
        
        $audioFile = UploadedFile::fake()->create('test.mp3', 1000, 'audio/mp3');
        
        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/audio", [
            'type' => 'file_upload',
            'audio' => $audioFile
        ]);
        
        $response->assertStatus(404);
    });
});

describe('Upload Video File', function () {
    it('succeeds with valid file', function () {
        $videoFile = UploadedFile::fake()->create('test.mp4', 5000, 'video/mp4');
        
        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/video", [
            'type' => 'file_upload',
            'video' => $videoFile
        ]);
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'video_url',
                         'video_file',
                         'video_type'
                     ]
                 ]);
        
        expect($response->json('data.video_type'))->toBe('file_upload');
        
        // Verify file was stored
        $filePath = $response->json('data.video_file');
        expect(Storage::disk('public')->exists($filePath))->toBeTrue();
        
        // Verify database was updated
        $this->multimedia->refresh();
        expect($this->multimedia->video_type)->toBe('file_upload');
        expect($this->multimedia->video_file)->not->toBeNull();
    });

    it('validates file size', function () {
        $largeFile = UploadedFile::fake()->create('test.mp4', 52000, 'video/mp4'); // 52MB > 50MB limit
        
        $response = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/video", [
            'type' => 'file_upload',
            'video' => $largeFile
        ]);
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['video']);
    });
});

describe('Upload Profile Image', function () {
    it('succeeds with valid file', function () {
        $imageFile = UploadedFile::fake()->image('profile.jpg', 200, 200);
        
        $response = $this->postJson("/api/content/profile/{$this->profile->id}/image", [
            'type' => 'file_upload',
            'image' => $imageFile
        ]);
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'url',
                         'file_path'
                     ]
                 ]);
        
        // Verify file was stored
        $filePath = $response->json('data.file_path');
        expect(Storage::disk('public')->exists($filePath))->toBeTrue();
        
        // Verify multimedia settings were updated
        $multimedia = \App\Models\ContentMultimedia::where('dynamic_content_id', $this->profile->dynamic_content_id)->first();
        expect($multimedia)->not->toBeNull();
        expect($multimedia->settings)->toHaveKey('profile_image');
        expect($multimedia->settings['profile_image'])->toBe($response->json('data.file_path'));
    });

    it('validates file type', function () {
        $invalidFile = UploadedFile::fake()->create('test.txt', 100, 'text/plain');
        
        $response = $this->postJson("/api/content/profile/{$this->profile->id}/image", [
            'type' => 'file_upload',
            'image' => $invalidFile
        ]);
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['image']);
    });
});

describe('Upload Gallery Image', function () {
    it('succeeds with valid file', function () {
        $imageFile = UploadedFile::fake()->image('gallery.jpg', 500, 300);
        
        $response = $this->postJson("/api/content/gallery/{$this->multimedia->id}", [
            'type' => 'file_upload',
            'alt_text' => 'Test gallery image',
            'image' => $imageFile
        ]);
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'image_url',
                         'image_path',
                         'type',
                         'alt_text'
                     ]
                 ]);
        
        expect($response->json('data.type'))->toBe('upload');
        expect($response->json('data.alt_text'))->toBe('Test gallery image');
        
        // Verify file was stored
        $filePath = $response->json('data.image_path');
        expect(Storage::disk('public')->exists($filePath))->toBeTrue();
        
        // Verify gallery image was created in database
        $galleryImage = ContentGalleryImage::find($response->json('data.id'));
        expect($galleryImage)->not->toBeNull();
        expect($galleryImage->content_multimedia_id)->toBe($this->multimedia->id);
        expect($galleryImage->type)->toBe('upload');
    });

    it('uses default alt text when not provided', function () {
        $imageFile = UploadedFile::fake()->image('gallery.jpg', 500, 300);
        
        $response = $this->postJson("/api/content/gallery/{$this->multimedia->id}", [
            'type' => 'file_upload',
            'image' => $imageFile
        ]);
        
        $response->assertStatus(200);
        expect($response->json('data.alt_text'))->toBe('Imagen de galería');
    });

    it('validates file size', function () {
        $largeFile = UploadedFile::fake()->create('large.jpg', 6000, 'image/jpeg'); // 6MB > 5MB limit
        
        $response = $this->postJson("/api/content/gallery/{$this->multimedia->id}", [
            'type' => 'file_upload',
            'image' => $largeFile
        ]);
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['image']);
    });
});

describe('Authentication', function () {
    it('requires authentication for all upload methods', function () {
        auth()->logout();
        
        $file = UploadedFile::fake()->image('test.jpg');
        
        // Audio upload should fail without auth
        $audioResponse = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/audio", [
            'type' => 'file_upload',
            'audio' => $file
        ]);
        $audioResponse->assertStatus(401);
        
        // Video upload should fail without auth
        $videoResponse = $this->postJson("/api/content/multimedia/{$this->multimedia->id}/video", [
            'type' => 'file_upload',
            'video' => $file
        ]);
        $videoResponse->assertStatus(401);
        
        // Profile image upload should fail without auth
        $profileResponse = $this->postJson("/api/content/profile/{$this->profile->id}/image", [
            'type' => 'file_upload',
            'image' => $file
        ]);
        $profileResponse->assertStatus(401);
        
        // Gallery image upload should fail without auth
        $galleryResponse = $this->postJson("/api/content/gallery/{$this->multimedia->id}", [
            'type' => 'file_upload',
            'image' => $file
        ]);
        $galleryResponse->assertStatus(401);
    });
});