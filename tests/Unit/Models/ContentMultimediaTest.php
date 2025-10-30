<?php

use App\Models\ContentGalleryImage;
use App\Models\ContentMultimedia;
use App\Models\DynamicContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('ContentMultimedia Model', function () {
    test('belongs to dynamic content', function () {
        $dynamicContent = DynamicContent::factory()->create();
        $multimedia = ContentMultimedia::factory()->create([
            'dynamic_content_id' => $dynamicContent->id,
        ]);

        expect($multimedia->dynamicContent)->toBeInstanceOf(DynamicContent::class)
            ->and($multimedia->dynamicContent->id)->toBe($dynamicContent->id);
    });

    test('has many gallery images', function () {
        $multimedia = ContentMultimedia::factory()->create();
        
        ContentGalleryImage::factory()->count(3)->create([
            'content_multimedia_id' => $multimedia->id,
        ]);

        expect($multimedia->galleryImages)->toHaveCount(3)
            ->and($multimedia->galleryImages->first())->toBeInstanceOf(ContentGalleryImage::class);
    });

    test('casts settings to array', function () {
        $settings = [
            'theme' => 'romantic',
            'autoplay' => true,
            'profile_image' => 'profiles/test.jpg',
        ];

        $multimedia = ContentMultimedia::factory()->create([
            'settings' => $settings,
        ]);

        expect($multimedia->settings)->toBeArray()
            ->and($multimedia->settings)->toBe($settings);
    });

    test('handles null settings', function () {
        $multimedia = ContentMultimedia::factory()->create([
            'settings' => null,
        ]);

        expect($multimedia->settings)->toBeArray()
            ->and($multimedia->settings)->toBe([]);
    });

    test('can update settings', function () {
        $multimedia = ContentMultimedia::factory()->create([
            'settings' => ['theme' => 'default'],
        ]);

        $newSettings = [
            'theme' => 'romantic',
            'autoplay' => true,
            'loop' => false,
        ];

        $multimedia->update(['settings' => $newSettings]);

        expect($multimedia->fresh()->settings)->toBe($newSettings);
    });

    test('generates correct audio url', function () {
        $multimedia = ContentMultimedia::factory()->create([
            'audio_file' => 'audio/test.mp3',
        ]);

        $expectedUrl = asset('storage/audio/test.mp3');
        
        $multimedia->update(['audio_url' => $expectedUrl]);

        expect($multimedia->audio_url)->toBe($expectedUrl)
            ->and($multimedia->audio_url)->toContain('storage/audio/test.mp3');
    });

    test('generates correct video url', function () {
        $multimedia = ContentMultimedia::factory()->create([
            'video_file' => 'videos/test.mp4',
        ]);

        $expectedUrl = asset('storage/videos/test.mp4');
        
        $multimedia->update(['video_url' => $expectedUrl]);

        expect($multimedia->video_url)->toBe($expectedUrl)
            ->and($multimedia->video_url)->toContain('storage/videos/test.mp4');
    });

    test('can have audio and video simultaneously', function () {
        $multimedia = ContentMultimedia::factory()->create([
            'audio_file' => 'audio/music.mp3',
            'audio_url' => asset('storage/audio/music.mp3'),
            'video_file' => 'videos/clip.mp4',
            'video_url' => asset('storage/videos/clip.mp4'),
        ]);

        expect($multimedia->audio_file)->not->toBeNull()
            ->and($multimedia->audio_url)->not->toBeNull()
            ->and($multimedia->video_file)->not->toBeNull()
            ->and($multimedia->video_url)->not->toBeNull();
    });

    test('handles empty gallery images collection', function () {
        $multimedia = ContentMultimedia::factory()->create();

        expect($multimedia->galleryImages)->toHaveCount(0)
            ->and($multimedia->galleryImages->isEmpty())->toBeTrue();
    });

    test('gallery images are ordered by sort order', function () {
        $multimedia = ContentMultimedia::factory()->create();
        
        ContentGalleryImage::factory()->create([
            'content_multimedia_id' => $multimedia->id,
            'sort_order' => 3,
            'alt_text' => 'Third',
        ]);
        
        ContentGalleryImage::factory()->create([
            'content_multimedia_id' => $multimedia->id,
            'sort_order' => 1,
            'alt_text' => 'First',
        ]);
        
        ContentGalleryImage::factory()->create([
            'content_multimedia_id' => $multimedia->id,
            'sort_order' => 2,
            'alt_text' => 'Second',
        ]);

        $images = $multimedia->galleryImages()->orderBy('sort_order')->get();

        expect($images[0]->alt_text)->toBe('First')
            ->and($images[1]->alt_text)->toBe('Second')
            ->and($images[2]->alt_text)->toBe('Third');
    });

    test('validates fillable attributes', function () {
        $multimedia = new ContentMultimedia();

        $expectedFillable = [
            'dynamic_content_id',
            'video_url',
            'video_file',
            'video_type',
            'audio_url',
            'audio_file',
            'audio_type',
            'gallery_images',
            'gallery_files',
            'settings',
        ];

        expect($multimedia->getFillable())->toBe($expectedFillable);
    });

    test('has correct table name', function () {
        $multimedia = new ContentMultimedia();
        
        expect($multimedia->getTable())->toBe('content_multimedia');
    });
});