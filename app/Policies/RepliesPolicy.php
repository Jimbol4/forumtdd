<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Reply;

class RepliesPolicy
{
    use HandlesAuthorization;

    public function manage(User $user, Reply $reply)
    {
        return $user->id == $reply->user_id;
    }
}
