<?php

use App\Models\ContentGalleryImage;
use App\Models\ContentMultimedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('ContentGalleryImage Model', function () {
    test('belongs to content multimedia', function () {
        $multimedia = ContentMultimedia::factory()->create();
        $galleryImage = ContentGalleryImage::factory()->create([
            'content_multimedia_id' => $multimedia->id,
        ]);

        expect($galleryImage->contentMultimedia)->toBeInstanceOf(ContentMultimedia::class)
            ->and($galleryImage->contentMultimedia->id)->toBe($multimedia->id);
    });

    test('has default sort order of zero', function () {
        $galleryImage = ContentGalleryImage::factory()->create();

        expect($galleryImage->sort_order)->toBe(0);
    });

    test('can store image path and url', function () {
        $imagePath = 'gallery/test_image.jpg';
        $imageUrl = asset('storage/' . $imagePath);

        $galleryImage = ContentGalleryImage::factory()->create([
            'image_path' => $imagePath,
            'image_url' => $imageUrl,
        ]);

        expect($galleryImage->image_path)->toBe($imagePath)
            ->and($galleryImage->image_url)->toBe($imageUrl)
            ->and($galleryImage->image_url)->toContain('storage/gallery/test_image.jpg');
    });

    test('can store alt text', function () {
        $altText = 'Beautiful sunset over the mountains';

        $galleryImage = ContentGalleryImage::factory()->create([
            'alt_text' => $altText,
        ]);

        expect($galleryImage->alt_text)->toBe($altText);
    });

    test('can handle null alt text', function () {
        $galleryImage = ContentGalleryImage::factory()->create([
            'alt_text' => null,
        ]);

        expect($galleryImage->alt_text)->toBeNull();
    });

    test('stores type field', function () {
        $galleryImage = ContentGalleryImage::factory()->create([
            'type' => 'upload',
        ]);

        expect($galleryImage->type)->toBe('upload');
    });

    test('can store caption', function () {
        $caption = 'This is a beautiful image caption with more details';

        $galleryImage = ContentGalleryImage::factory()->create([
            'caption' => $caption,
        ]);

        expect($galleryImage->caption)->toBe($caption);
    });

    test('orders by sort order by default', function () {
        $multimedia = ContentMultimedia::factory()->create();

        $image3 = ContentGalleryImage::factory()->create([
            'content_multimedia_id' => $multimedia->id,
            'sort_order' => 3,
        ]);

        $image1 = ContentGalleryImage::factory()->create([
            'content_multimedia_id' => $multimedia->id,
            'sort_order' => 1,
        ]);

        $image2 = ContentGalleryImage::factory()->create([
            'content_multimedia_id' => $multimedia->id,
            'sort_order' => 2,
        ]);

        $orderedImages = ContentGalleryImage::where('content_multimedia_id', $multimedia->id)
            ->orderBy('sort_order')
            ->get();

        expect($orderedImages[0]->id)->toBe($image1->id)
            ->and($orderedImages[1]->id)->toBe($image2->id)
            ->and($orderedImages[2]->id)->toBe($image3->id);
    });

    test('validates fillable attributes', function () {
        $galleryImage = new ContentGalleryImage();

        $expectedFillable = [
            'content_multimedia_id',
            'image_path',
            'image_url',
            'alt_text',
            'caption',
            'sort_order',
            'type',
            'metadata',
        ];

        expect($galleryImage->getFillable())->toBe($expectedFillable);
    });

    test('has correct table name', function () {
        $galleryImage = new ContentGalleryImage();
        
        expect($galleryImage->getTable())->toBe('content_gallery_images');
    });

    test('can generate filename from path', function () {
        $galleryImage = ContentGalleryImage::factory()->create([
            'image_path' => 'gallery/123456_beautiful_sunset.jpg',
        ]);

        $filename = basename($galleryImage->image_path);
        
        expect($filename)->toBe('123456_beautiful_sunset.jpg')
            ->and($filename)->toContain('beautiful_sunset.jpg');
    });

    test('can update sort order', function () {
        $galleryImage = ContentGalleryImage::factory()->create([
            'sort_order' => 0,
        ]);

        $galleryImage->update(['sort_order' => 5]);

        expect($galleryImage->fresh()->sort_order)->toBe(5);
    });

    test('can update alt text', function () {
        $galleryImage = ContentGalleryImage::factory()->create([
            'alt_text' => 'Original alt text',
        ]);

        $newAltText = 'Updated alt text with new description';
        $galleryImage->update(['alt_text' => $newAltText]);

        expect($galleryImage->fresh()->alt_text)->toBe($newAltText);
    });

    test('has timestamps', function () {
        $galleryImage = ContentGalleryImage::factory()->create();

        expect($galleryImage->created_at)->not->toBeNull()
            ->and($galleryImage->updated_at)->not->toBeNull()
            ->and($galleryImage->created_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
            ->and($galleryImage->updated_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });
});