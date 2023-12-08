<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Collection|array
    {
        return Comment::with(['post', 'user:id,name'])->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(StoreCommentRequest $request): JsonResponse
    {
        try {

            $comment = Auth::user()->comments()->create($request->validated());
            return response()->json([
                'id'      => $comment->id,
                'message' => "Comment created successfully"
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
    public function show(Comment $comment): Comment
    {
        return $comment->load(['post', 'user:id,name']);
    }
}
