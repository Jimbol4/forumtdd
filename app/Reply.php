<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Favourite;
use App\Favouritable;
use App\RecordsActivity;

class Reply extends Model
{
    use Favouritable, RecordsActivity;

    protected $with = ['owner', 'favourites'];

    protected $fillable = ['body', 'user_id'];

    protected $appends = ['favoritesCount', 'isFavourited'];

    public function thread()
    {
        return $this->belongsTo('App\Thread');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function path()
    {
        return $this->thread->path() . '#reply-' . $this->id;
    }
}
