<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class CommentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user;
    private $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->post = Post::factory()->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function it_shows_a_comment()
    {
        $comment = Comment::factory()->create(['post_id' => $this->post->id, 'user_id' => $this->user->id]);

        $this->actingAs($this->user, 'api')
            ->get(route('comments.show', ['post_id' => $this->post->id, 'comment_id' => $comment->id]))
            ->assertStatus(200)
            ->assertJson($comment->toArray());
    }

    /** @test */
    public function it_updates_a_comment()
    {
        $comment = Comment::factory()->create(['post_id' => $this->post->id, 'user_id' => $this->user->id]);
        $newData = ['body' => $this->faker->paragraph];

        $this->actingAs($this->user, 'api')
            ->putJson(route('comments.update', ['post_id' => $this->post->id, 'comment_id' => $comment->id]), $newData)
            ->assertStatus(200)
            ->assertJson($newData);
    }

    /** @test */
    public function it_deletes_a_comment()
    {
        $comment = Comment::factory()->create(['post_id' => $this->post->id, 'user_id' => $this->user->id]);

        $this->actingAs($this->user, 'api')
            ->delete(route('comments.destroy', ['post_id' => $this->post->id, 'comment_id' => $comment->id]))
            ->assertStatus(200)
            ->assertJson(['message' => 'Comment deleted successfully']);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
