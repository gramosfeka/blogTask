<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function index(): JsonResponse
    {
        $comments = Comment::all();
        return response()->json($comments);
    }

    public function store(CommentRequest $request, $post_id): JsonResponse
    {
        $post = Post::find($post_id);

        $comment = new Comment($request->validated());
        $comment->user_id = Auth::id();
        $comment->post()->associate($post);
        $comment->save();


        return response()->json($comment, 201);
    }

    public function show($id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        return response()->json($comment);
    }

    public function update(CommentRequest $request, $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        $comment->update($request->validated());

        return response()->json($comment);
    }

    public function destroy($id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
