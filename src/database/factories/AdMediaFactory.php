<?php

namespace Database\Factories;

use App\Models\AdMedia;
use App\Models\CarAdvert;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdMedia>
 */
class AdMediaFactory extends Factory
{
    protected $model = AdMedia::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mediaType = fake()->randomElement(['image', 'video']);

        return [
            'media_url' => $mediaType === 'image'
                ? 'https://fastly.picsum.photos/id/133/2742/1828.jpg'
                : 'https://example.com/dummy-video.mp4',
            'media_type' => $mediaType,
            'ad_id' => CarAdvert::query()->inRandomOrder()->value('ad_id') ?? CarAdvert::factory(),
        ];
    }
}
