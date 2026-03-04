<?php

namespace Database\Factories;

use App\Models\AppUser;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['like', 'comment', 'follow', 'ad_interest']);

        return [
            'user_id' => AppUser::query()->inRandomOrder()->value('user_id') ?? AppUser::factory(),
            'notif_type' => $type,
            'notif_text' => match ($type) {
                'like' => 'A alguien le ha gustado tu publicación.',
                'comment' => 'Han comentado en tu publicación.',
                'follow' => 'Tienes un nuevo seguidor.',
                default => 'Un usuario está interesado en tu anuncio.',
            },
            'is_read' => fake()->boolean(35),
            'created_at' => fake()->dateTimeBetween('-45 days', 'now'),
        ];
    }
}
