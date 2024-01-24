<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createPost(array $data = [])
    {
        $user = User::factory()->create();
        return Post::factory()->create(array_merge(['user_id' => $user->id], $data));
    }

    public function testIndex()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create some posts
        $posts = Post::factory()->count(3)->create();

        $response = $this->json('GET', route('posts.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'body',
                        'user_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJson(['data' => $posts->toArray()]);
    }

    public function testShow()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $post = $this->createPost();

        $response = $this->json('GET', route('posts.show', ['id' => $post->id]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'body' => $post->body,
                    'user_id' => $post->user_id,
                ],
            ]);
    }

    public function testStore()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $postData = [
            'title' => 'Test Title',
            'body' => 'Test Body',
        ];

        $response = $this->json('POST', route('posts.store'), $postData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post created successfully',
                'data' => [
                    'title' => $postData['title'],
                    'body' => $postData['body'],
                    'user_id' => $user->id,
                ],
            ]);

        $this->assertDatabaseHas('posts', $postData);
    }

    public function testUpdate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $post = $this->createPost();

        $updateData = [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
        ];

        $response = $this->json('PUT', route('posts.update', ['id' => $post->id]), $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post updated successfully',
                'data' => [
                    'title' => $updateData['title'],
                    'body' => $updateData['body'],
                    'user_id' => $post->user_id,
                ],
            ]);
        $this->assertDatabaseHas('posts', $updateData);
    }

    public function testDestroy()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $post = $this->createPost();

        $response = $this->json('DELETE', route('posts.destroy', ['id' => $post->id]));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post deleted successfully']);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
