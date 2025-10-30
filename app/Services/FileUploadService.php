<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
// use Intervention\Image\Facades\Image;

class FileUploadService
{
    /**
     * Upload and optimize an image file
     */
    public function uploadImage(
        UploadedFile $file, 
        string $directory = 'images',
        array $options = []
    ): array {
        // Validate image file
        if (!$this->isImageFile($file)) {
            throw new \InvalidArgumentException('Invalid image file type');
        }

        $options = array_merge([
            'max_width' => 1920,
            'max_height' => 1080,
            'quality' => 80,
            'optimize' => true,
            'generate_thumbnail' => false,
            'thumbnail_size' => 300,
        ], $options);

        // Generate unique filename
        $filename = $this->generateUniqueFilename($file, $options['prefix'] ?? null);
        $path = $directory . '/' . $filename;

        // For now, just store original file without optimization until Intervention Image is installed
        $storedPath = $file->storeAs($directory, $filename, 'public');
        
        return [
            'path' => $storedPath,
            'url' => asset('storage/' . $storedPath),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'optimized' => false,
        ];
    }

    /**
     * Upload audio file
     */
    public function uploadAudio(
        UploadedFile $file, 
        string $directory = 'audio',
        array $options = []
    ): array {
        $this->validateAudioFile($file, $options);

        $filename = $this->generateUniqueFilename($file, $options['prefix'] ?? null);
        $storedPath = $file->storeAs($directory, $filename, 'public');

        return [
            'path' => $storedPath,
            'url' => asset('storage/' . $storedPath),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'duration' => $this->getAudioDuration($file),
        ];
    }

    /**
     * Upload video file
     */
    public function uploadVideo(
        UploadedFile $file, 
        string $directory = 'videos',
        array $options = []
    ): array {
        $this->validateVideoFile($file, $options);

        $filename = $this->generateUniqueFilename($file, $options['prefix'] ?? null);
        $storedPath = $file->storeAs($directory, $filename, 'public');

        $result = [
            'path' => $storedPath,
            'url' => asset('storage/' . $storedPath),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];

        // Try to get video metadata
        try {
            $metadata = $this->getVideoMetadata($file);
            $result = array_merge($result, $metadata);
        } catch (\Exception $e) {
            // Continue without metadata if extraction fails
            \Log::warning('Could not extract video metadata: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Delete file from storage
     */
    public function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Generate unique filename with timestamp prefix
     */
    private function generateUniqueFilename(UploadedFile $file, ?string $prefix = null): string
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Clean basename for safe file names
        $basename = Str::slug($basename);
        
        $timestamp = time();
        $random = Str::random(8);
        
        $filename = $timestamp . '_';
        
        if ($prefix) {
            $filename .= $prefix . '_';
        }
        
        $filename .= $basename . '_' . $random . '.' . $extension;
        
        return $filename;
    }

    /**
     * Optimize image using Intervention Image (disabled until package is installed)
     */
    // private function optimizeImage(UploadedFile $file, array $options): string
    // {
    //     $image = Image::make($file->getRealPath());

    //     // Resize if larger than max dimensions
    //     if ($image->width() > $options['max_width'] || $image->height() > $options['max_height']) {
    //         $image->resize($options['max_width'], $options['max_height'], function ($constraint) {
    //             $constraint->aspectRatio();
    //             $constraint->upsize();
    //         });
    //     }

    //     // Apply quality compression
    //     $image->encode('jpg', $options['quality']);

    //     return $image->encoded;
    // }

    /**
     * Generate thumbnail for image (disabled until Intervention Image is installed)
     */
    // private function generateThumbnail(
    //     string $imageData, 
    //     string $directory, 
    //     string $originalFilename, 
    //     int $size
    // ): string {
    //     $thumbnail = Image::make($imageData);
    //     $thumbnail->fit($size, $size);
    //     $thumbnail->encode('jpg', 85);

    //     $thumbnailFilename = 'thumb_' . $originalFilename;
    //     $thumbnailPath = $directory . '/thumbnails/' . $thumbnailFilename;

    //     Storage::disk('public')->put($thumbnailPath, $thumbnail->encoded);

    //     return $thumbnailPath;
    // }

    /**
     * Check if file is an image
     */
    private function isImageFile(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/bmp',
        ]);
    }

    /**
     * Validate audio file
     */
    private function validateAudioFile(UploadedFile $file, array $options): void
    {
        $maxSize = $options['max_size'] ?? 10 * 1024 * 1024; // 10MB default

        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException('Audio file size exceeds maximum allowed size');
        }

        $allowedMimeTypes = $options['allowed_types'] ?? [
            'audio/mpeg',
            'audio/wav',
            'audio/mp4',
            'audio/aac',
            'audio/ogg',
            'audio/webm',
        ];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \InvalidArgumentException('Invalid audio file type');
        }
    }

    /**
     * Validate video file
     */
    private function validateVideoFile(UploadedFile $file, array $options): void
    {
        $maxSize = $options['max_size'] ?? 50 * 1024 * 1024; // 50MB default

        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException('Video file size exceeds maximum allowed size');
        }

        $allowedMimeTypes = $options['allowed_types'] ?? [
            'video/mp4',
            'video/quicktime',
            'video/avi',
            'video/webm',
            'video/mkv',
            'video/x-msvideo',
        ];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \InvalidArgumentException('Invalid video file type');
        }
    }

    /**
     * Get audio duration (simplified - would need FFMpeg for real implementation)
     */
    private function getAudioDuration(UploadedFile $file): ?float
    {
        // This is a placeholder - real implementation would use FFMpeg
        // For now, return null as duration is not critical for basic functionality
        return null;
    }

    /**
     * Get video metadata (simplified - would need FFMpeg for real implementation)
     */
    private function getVideoMetadata(UploadedFile $file): array
    {
        // This is a placeholder - real implementation would use FFMpeg
        // For now, return basic metadata
        return [
            'duration' => null,
            'width' => null,
            'height' => null,
            'fps' => null,
        ];
    }

    /**
     * Batch upload multiple files
     */
    public function uploadMultiple(
        array $files, 
        string $directory,
        string $type = 'image',
        array $options = []
    ): array {
        // Validate type first
        if (!in_array($type, ['image', 'audio', 'video'])) {
            throw new \InvalidArgumentException("Unsupported file type: {$type}");
        }

        $results = [];
        $errors = [];

        foreach ($files as $index => $file) {
            try {
                switch ($type) {
                    case 'image':
                        $result = $this->uploadImage($file, $directory, $options);
                        break;
                    case 'audio':
                        $result = $this->uploadAudio($file, $directory, $options);
                        break;
                    case 'video':
                        $result = $this->uploadVideo($file, $directory, $options);
                        break;
                }

                $results[] = $result;
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'filename' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'uploaded' => $results,
            'errors' => $errors,
            'total' => count($files),
            'success_count' => count($results),
            'error_count' => count($errors),
        ];
    }

    /**
     * Get file info without uploading
     */
    public function getFileInfo(UploadedFile $file): array
    {
        return [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'is_image' => $this->isImageFile($file),
            'human_size' => $this->formatFileSize($file->getSize()),
        ];
    }

    /**
     * Format file size in human readable format
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}