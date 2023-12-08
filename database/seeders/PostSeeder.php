<?php

namespace Database\Seeders;

use App\Jobs\ExtractKeywordsFromPostJob;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Services\KeywordExtractionService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::factory()->createMany(50)->each(function (Post $post) {
            ExtractKeywordsFromPostJob::dispatchSync($post->id);

            $comments = Comment::factory()
                ->forPost($post)
                ->createMany(fake()->numberBetween(1, 50));

            $post->comments()->saveMany($comments);

            $likes = Like::factory()->forPost($post)->createMany(fake()->numberBetween(0, 200));
            $post->likes()->saveMany($likes);
        });
    }
}
