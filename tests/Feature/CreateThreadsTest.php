<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;
use App\Activity;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function publishThread($overrides)
    {
        $this->signIn();

        $thread = make('App\Thread', $overrides);

        return $this->post('/threads', $thread->toArray());
    }

    public function testGuestsMayNotCreateThreads()
    {
        $this->withoutExceptionHandling();
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->post('/threads', make('App\Thread')->toArray());
    }

    public function testGuestsCannotSeeThreadCreationForm()
    {
        $this->withExceptionHandling();
        $response = $this->get('/threads/create');
        $response->assertRedirect('/login');
    }

    public function testAnAuthenticatedUserCanCreateNewForumThreads()
    {
        $this->signIn();

        $channel = create('App\Channel');

        $thread = make('App\Thread', ['channel_id' => $channel->id]);

        $response = $this->post('/threads', $thread->toArray());

        $response = $this->get($response->headers->get('location'));

        $response
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    public function testAThreadRequiresATitle()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    public function testAThreadRequiresABody()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    public function testAThreadRequiresAValidChannelId()
    {
        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 99999])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function a_thread_can_be_deleted_by_its_owner()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $this->delete($thread->path())
            ->assertRedirect('/threads');

        $this->assertDatabaseMissing('threads', ['title' => $thread->title]);
        $this->assertDatabaseMissing('replies', ['thread_id' => $thread->id]);
        $this->assertDatabaseMissing('activities', ['subject_id' => $thread->id]);

        $this->assertEquals(0, Activity::count());

    }

    /** @test */
    public function guests_or_non_owners_cannot_delete_threads()
    {
        $thread = create('App\Thread');

        $this->delete($thread->path())
            ->assertRedirect('/login');

        $this->signIn();

        $this->delete($thread->path())
            ->assertStatus(403);

        $this->assertDatabaseHas('threads', ['id' => $thread->id]);
    }
}
