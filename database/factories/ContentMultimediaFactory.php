<?php

namespace Database\Factories;

use App\Models\ContentMultimedia;
use App\Models\DynamicContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentMultimediaFactory extends Factory
{
    protected $model = ContentMultimedia::class;

    public function definition(): array
    {
        return [
            'dynamic_content_id' => DynamicContent::factory(),
            'video_url' => fake()->optional()->url(),
            'video_file' => fake()->optional()->filePath(),
            'video_type' => fake()->randomElement(['file_upload', 'youtube', 'vimeo', 'direct']),
            'audio_url' => fake()->optional()->url(),
            'audio_file' => fake()->optional()->filePath(),
            'audio_type' => fake()->randomElement(['file_upload', 'youtube_music', 'spotify', 'soundcloud', 'direct']),
            'settings' => [
                'video' => [
                    'autoplay' => fake()->boolean(),
                    'muted' => fake()->boolean(),
                ],
                'audio' => [
                    'autoplay' => fake()->boolean(),
                    'loop' => fake()->boolean(),
                ],
            ],
        ];
    }
}