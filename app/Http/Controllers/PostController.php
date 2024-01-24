<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::all();

        return response()->json(['data' => $posts]);
    }

    public function show($id)
    {
        $posts = Post::findOrFail($id);

        return response()->json(['data' => $posts]);
    }

    public function store(PostRequest $request)
    {
       $user = Auth::user();

        $posts = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $user->id,
        ]);

        return response()->json(['message' => 'Post created successfully', 'data' => $posts]);
    }

    public function update(PostRequest $request, $id)
    {
        $post = Post::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $post->update($data);

        return response()->json(['message' => 'Post updated successfully', 'data' => $post]);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }


}
