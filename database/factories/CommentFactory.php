<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->realText(150),
            'user_id' => User::inRandomOrder()->limit(1)->select('id')->first()->id
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function forPost(Post $post): static
    {
        return $this->state(fn(array $attributes) => [
            'post_id' => $post->id,
            'created_at' =>fake()->dateTimeBetween($post->created_at)
        ]);
    }
}
