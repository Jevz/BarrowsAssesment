<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Like>
 */
class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::normal()->inRandomOrder()->limit(1)->select('id')->first()->id,
        ];
    }

    public function forPost(Post $post): static
    {
        return $this->state(fn(array $attributes) => [
            'post_id' => $post->id,
        ]);
    }
}
