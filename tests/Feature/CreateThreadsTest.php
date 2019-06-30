<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;

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
}
