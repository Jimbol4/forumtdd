<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParticipateInForumTest extends TestCase
{
    use RefreshDatabase;

    function testUnauthenticatedUsersMayNotAddReplies()
    {
        $this->withExceptionHandling();
        $this->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    function testAnAuthenticatedUserMayParticipateInForumThreads()
    {
        $this->be($user = create('App\User'));

        $thread = create('App\Thread');

        $reply = make('App\Reply', ['user_id' => $user->id, 'thread_id' => $thread->id]);

        $this->post($thread->path() . '/replies', $reply->toArray());

        $response = $this->get($thread->path());

        $response->assertSee($reply->body);
    }

    function testAReplyRequiresABody()
    {
        $this->be($user = create('App\User'));

        $thread = create('App\Thread');

        $reply = make('App\Reply', ['user_id' => $user->id, 'thread_id' => $thread->id, 'body' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function unauthorized_users_cannot_delete_replies()
    {
        $reply = create('App\Reply');

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
    } 

    /** @test */
    public function authorized_users_can_delete_replies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['body' => $reply->body]);
    }

}
