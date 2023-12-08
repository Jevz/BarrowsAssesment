<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Jobs\ExtractKeywordsFromPostJob;
use App\Models\Post;
use App\Services\PostLikeEstimatorService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mockery\Exception;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Collection|array
    {
        $filterWord = Str::lower(request()->filter);

        return Post::query()->select('*')
                   ->with('user:id,name')
                   ->when(request()->with_comments, function ($query) {
                       $query->with('comments');
                   })
                   ->when(request()->filter != null, function ($query) use ($filterWord) {
                       // This works by calculating the length of the content minus the length of the content with the search word removed
                       // Using this length and the length of the search word, we can then work out the number of occurrences
                       $query
                           ->selectRaw("FLOOR((LENGTH(LOWER(content)) - LENGTH(REPLACE(LOWER(content), '$filterWord', ''))) / LENGTH('$filterWord')) as search_phrase_count")
                           ->orderByDesc("search_phrase_count");
                   })
                   ->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(StorePostRequest $request): JsonResponse
    {
        try {

            $post = $request->user()->posts()->create($request->validated());
            ExtractKeywordsFromPostJob::dispatchSync($post->id);

            $likeEstimation = (new PostLikeEstimatorService($post))->predict();
            return response()->json([
                'id'      => $post->id,
                'message'         => "Post created successfully",
                'like_estimation' => $likeEstimation
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'message' => "Something went wrong: {$exception->getMessage()}",
                'code'    => $exception->getCode()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): Post
    {
        return $post->load(['comments', 'user:id,name'])->loadCount('likes');
    }

    /**
     * Flag the resource as misleading.
     */
    public function flag(Post $post): JsonResponse
    {
        $authUser = Auth::user();
        abort_if(!$authUser->isAdmin(), 401, "Only admins are allowed to flag posts.");

        try {

            $post->update([
                'flagged_as_misleading' => true,
                'flagged_by'            => $authUser->id
            ]);

            return response()->json(['message' => "Post flagged successfully!"]);

        } catch (Exception $exception) {
            return response()->json([
                'message' => "Something went wrong: {$exception->getMessage()}",
                'code'    => $exception->getCode()
            ]);
        }
    }

    /**
     * Like the resource.
     */
    public function like(Post $post): JsonResponse
    {
        $authUser = Auth::user();

        $userAlreadyLikedPost = $post->likes()->where('user_id', $authUser->id)->exists();
        abort_if($userAlreadyLikedPost, 422, "Post already liked.");

        try {
            $post->likes()->create(['user_id'=>$authUser->id]);
            return response()->json(['message' => "Post liked successfully!"]);

        } catch (Exception $exception) {
            return response()->json([
                'message' => "Something went wrong: {$exception->getMessage()}",
                'code'    => $exception->getCode()
            ]);
        }
    }
}
