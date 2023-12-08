<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\KeywordExtractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class EstimatePostLikesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $postId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $post = Post::findOrFail($this->postId);

        $service = new KeywordExtractionService($post);
        $extractedKeywords = $service->extractTopKeywords();

        $post->update(['keywords' => Arr::join($extractedKeywords, ',')]);
    }
}
