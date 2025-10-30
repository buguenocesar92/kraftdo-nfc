<?php

use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

describe('FileUploadService', function () {
    beforeEach(function () {
        $this->service = new FileUploadService();
        Storage::fake('public');
    });

    test('can upload image file', function () {
        $file = UploadedFile::fake()->image('test.jpg', 800, 600)->size(1000);

        $result = $this->service->uploadImage($file, 'test-images');

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['path', 'url', 'filename', 'original_name', 'size', 'mime_type'])
            ->and($result['original_name'])->toBe('test.jpg')
            ->and($result['path'])->toStartWith('test-images/')
            ->and($result['url'])->toContain('storage/test-images/');

        // Verify file was stored
        Storage::disk('public')->assertExists($result['path']);
    });

    test('generates unique filenames', function () {
        $file1 = UploadedFile::fake()->image('same.jpg');
        $file2 = UploadedFile::fake()->image('same.jpg');

        $result1 = $this->service->uploadImage($file1, 'test');
        $result2 = $this->service->uploadImage($file2, 'test');

        expect($result1['filename'])->not->toBe($result2['filename'])
            ->and($result1['filename'])->toContain('same')
            ->and($result2['filename'])->toContain('same');
    });

    test('can upload with prefix', function () {
        $file = UploadedFile::fake()->image('test.jpg');

        $result = $this->service->uploadImage($file, 'test', ['prefix' => 'profile']);

        expect($result['filename'])->toContain('profile');
    });

    test('can upload audio file', function () {
        $file = UploadedFile::fake()->create('audio.mp3', 5000, 'audio/mpeg');

        $result = $this->service->uploadAudio($file, 'test-audio');

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['path', 'url', 'duration'])
            ->and($result['original_name'])->toBe('audio.mp3')
            ->and($result['mime_type'])->toBe('audio/mpeg')
            ->and($result['path'])->toStartWith('test-audio/');

        Storage::disk('public')->assertExists($result['path']);
    });

    test('validates audio file size', function () {
        $file = UploadedFile::fake()->create('large_audio.mp3', 15000, 'audio/mpeg'); // 15MB > 10MB default

        expect(fn() => $this->service->uploadAudio($file, 'test-audio'))
            ->toThrow(InvalidArgumentException::class, 'Audio file size exceeds maximum allowed size');
    });

    test('validates audio file type', function () {
        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        expect(fn() => $this->service->uploadAudio($file, 'test-audio'))
            ->toThrow(InvalidArgumentException::class, 'Invalid audio file type');
    });

    test('can upload video file', function () {
        $file = UploadedFile::fake()->create('video.mp4', 25000, 'video/mp4');

        $result = $this->service->uploadVideo($file, 'test-videos');

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['path', 'url'])
            ->and($result['original_name'])->toBe('video.mp4')
            ->and($result['mime_type'])->toBe('video/mp4')
            ->and($result['path'])->toStartWith('test-videos/');

        Storage::disk('public')->assertExists($result['path']);
    });

    test('validates video file size', function () {
        $file = UploadedFile::fake()->create('large_video.mp4', 60000, 'video/mp4'); // 60MB > 50MB default

        expect(fn() => $this->service->uploadVideo($file, 'test-videos'))
            ->toThrow(InvalidArgumentException::class, 'Video file size exceeds maximum allowed size');
    });

    test('validates video file type', function () {
        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        expect(fn() => $this->service->uploadVideo($file, 'test-videos'))
            ->toThrow(InvalidArgumentException::class, 'Invalid video file type');
    });

    test('can delete file', function () {
        $file = UploadedFile::fake()->image('test.jpg');
        $result = $this->service->uploadImage($file, 'test');

        // Verify file exists
        Storage::disk('public')->assertExists($result['path']);

        // Delete file
        $deleted = $this->service->deleteFile($result['path']);

        expect($deleted)->toBeTrue();
        Storage::disk('public')->assertMissing($result['path']);
    });

    test('returns false when deleting non existent file', function () {
        $deleted = $this->service->deleteFile('non/existent/file.jpg');

        expect($deleted)->toBeFalse();
    });

    test('can upload multiple files', function () {
        $files = [
            UploadedFile::fake()->image('image1.jpg'),
            UploadedFile::fake()->image('image2.png'),
            UploadedFile::fake()->image('image3.gif'),
        ];

        $result = $this->service->uploadMultiple($files, 'test-batch', 'image');

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['uploaded', 'errors', 'total', 'success_count', 'error_count'])
            ->and($result['total'])->toBe(3)
            ->and($result['success_count'])->toBe(3)
            ->and($result['error_count'])->toBe(0)
            ->and($result['uploaded'])->toHaveCount(3)
            ->and($result['errors'])->toBeEmpty();

        // Verify all files were stored
        foreach ($result['uploaded'] as $uploadedFile) {
            Storage::disk('public')->assertExists($uploadedFile['path']);
        }
    });

    test('handles errors in batch upload', function () {
        $files = [
            UploadedFile::fake()->image('good.jpg'),
            UploadedFile::fake()->create('bad.pdf', 1000, 'application/pdf'), // Invalid for image type
            UploadedFile::fake()->image('good2.png'),
        ];

        $result = $this->service->uploadMultiple($files, 'test-batch', 'image');

        expect($result['total'])->toBe(3)
            ->and($result['success_count'])->toBe(2)
            ->and($result['error_count'])->toBe(1)
            ->and($result['uploaded'])->toHaveCount(2)
            ->and($result['errors'])->toHaveCount(1);

        $error = $result['errors'][0];
        expect($error['index'])->toBe(1)
            ->and($error['filename'])->toBe('bad.pdf')
            ->and($error)->toHaveKey('error');
    });

    test('can get file info without uploading', function () {
        $file = UploadedFile::fake()->image('test.jpg', 800, 600)->size(1500);

        $info = $this->service->getFileInfo($file);

        expect($info)->toBeArray()
            ->and($info['original_name'])->toBe('test.jpg')
            ->and($info['extension'])->toBe('jpg')
            ->and($info['mime_type'])->toBe('image/jpeg')
            ->and($info['is_image'])->toBeTrue()
            ->and($info)->toHaveKey('human_size');
    });

    test('formats file size correctly', function () {
        $file = UploadedFile::fake()->create('test.txt', 500);
        $info = $this->service->getFileInfo($file);
        
        // Test that the formatting method works
        expect($info)->toHaveKey('human_size')
            ->and($info['human_size'])->toBeString();
    });

    test('respects custom audio file size limits', function () {
        $file = UploadedFile::fake()->create('audio.mp3', 3000, 'audio/mpeg'); // 3MB

        $options = ['max_size' => 2 * 1024 * 1024]; // 2MB limit

        expect(fn() => $this->service->uploadAudio($file, 'test', $options))
            ->toThrow(InvalidArgumentException::class);
    });

    test('respects custom video file size limits', function () {
        $file = UploadedFile::fake()->create('video.mp4', 20000, 'video/mp4'); // 20MB

        $options = ['max_size' => 15 * 1024 * 1024]; // 15MB limit

        expect(fn() => $this->service->uploadVideo($file, 'test', $options))
            ->toThrow(InvalidArgumentException::class);
    });

    test('accepts custom allowed audio types', function () {
        $file = UploadedFile::fake()->create('audio.wav', 1000, 'audio/wav');

        $options = ['allowed_types' => ['audio/wav']]; // Only WAV allowed

        $result = $this->service->uploadAudio($file, 'test', $options);

        expect($result['mime_type'])->toBe('audio/wav');
    });

    test('rejects disallowed audio types', function () {
        $file = UploadedFile::fake()->create('audio.mp3', 1000, 'audio/mpeg');

        $options = ['allowed_types' => ['audio/wav']]; // Only WAV allowed, MP3 not

        expect(fn() => $this->service->uploadAudio($file, 'test', $options))
            ->toThrow(InvalidArgumentException::class);
    });

    test('accepts custom allowed video types', function () {
        $file = UploadedFile::fake()->create('video.webm', 10000, 'video/webm');

        $options = ['allowed_types' => ['video/webm']]; // Only WebM allowed

        $result = $this->service->uploadVideo($file, 'test', $options);

        expect($result['mime_type'])->toBe('video/webm');
    });

    test('throws exception for unsupported batch type', function () {
        $files = [UploadedFile::fake()->create('test.pdf')];

        expect(fn() => $this->service->uploadMultiple($files, 'test', 'document'))
            ->toThrow(InvalidArgumentException::class, 'Unsupported file type: document');
    });
});