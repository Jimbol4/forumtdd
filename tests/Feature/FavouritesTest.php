<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavouritesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_favourite_anything()
    {
        $reply = create('App\Reply');

        $this->post('/replies/' . $reply->id . '/favourites')
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_favourite_any_reply()
    {
        $this->signIn();

        $reply = create('App\Reply');

        $this->post('/replies/' . $reply->id . '/favourites');

        $this->assertCount(1, $reply->favourites);
    }

    /** @test */
    public function an_authenticated_user_can_unfavourite_a_reply()
    {
        $this->signIn();

        $reply = create('App\Reply');

        $this->post('/replies/' . $reply->id . '/favourites');

        $this->assertCount(1, $reply->favourites);

        $this->delete('/replies/' . $reply->id . '/favourites');

        $this->assertCount(0, $reply->fresh()->favourites);
    }

    /** @test */
    public function an_authenticated_user_can_only_favourite_a_reply_once()
    {
        $this->signIn();

        $reply = create('App\Reply');

        $this->post('/replies/' . $reply->id . '/favourites');
        $this->post('/replies/' . $reply->id . '/favourites');

        $this->assertCount(1, $reply->favourites);
    }
}
