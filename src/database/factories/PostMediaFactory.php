<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostMedia>
 */
class PostMediaFactory extends Factory
{
    protected $model = PostMedia::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mediaType = fake()->randomElement(['image', 'video']);

        return [
            'post_id' => Post::query()->inRandomOrder()->value('post_id') ?? Post::factory(),
            'media_url' => $mediaType === 'image'
                ? 'https://picsum.photos/seed/'.fake()->uuid().'/1280/720'
                : 'https://example.com/videos/'.fake()->uuid().'.mp4',
            'media_type' => $mediaType,
        ];
    }
}
