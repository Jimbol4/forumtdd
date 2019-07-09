<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Favourite;
use App\Favouritable;

class Reply extends Model
{
    use Favouritable;

    protected $with = ['owner', 'favourites'];

    protected $fillable = ['body', 'user_id'];

    public function thread()
    {
        return $this->belongsTo('App\Thread');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
