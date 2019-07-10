<?php

namespace App\Policies;

use App\User;
use App\Thread;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadsPolicy
{
    use HandlesAuthorization;

    public function before($user)
    {
        // check for an admin here
    }

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function manage(User $user, Thread $thread) 
    {
        return $user->id == $thread->user_id;
    }
}
