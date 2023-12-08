<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Services\KeywordExtractionService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $modelData = [
            'user_id' => User::inRandomOrder()->select('id')->limit(1)->first()->id,
            'content' => $this->createContent(),
            'created_at' => fake()->dateTimeBetween('-1 day')
        ];

        // If true, post has not been flagged
        if (fake()->boolean(70)) {
            return $modelData;
        }

        $modelData['flagged_as_misleading'] = true;
        $modelData['flagged_by'] = User::admin()->inRandomOrder()->limit(1)->select('id')->first()->id;

        return $modelData;
    }

    public function flagged(): PostFactory|Factory
    {
        return $this->state(fn(array $attributes) => [
            'flagged_as_misleading' => true,
            'flagged_by'            => User::admin()->inRandomOrder()->limit(1)->select('id')->first()->id
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    private function createContent(): string
    {
        $paragraphs = [];
        for ($i = 0; $i <= fake()->numberBetween(1, 5); $i++) {
            $paragraphs[] = fake()->realTextBetween(100, 500);
        }

        return Arr::join($paragraphs, '\n\n');
    }
}
