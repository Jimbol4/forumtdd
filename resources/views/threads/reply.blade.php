<div class="card">
    <div class="card-header">
        <div class="level">
            <h5 class="flex">
                <a href="/profiles/{{ $reply->owner->name }}">{{ $reply->owner->name }}</a> said {{ $reply->created_at->diffForHumans() }}...
            </h5>
            <div>
                {{ $reply->favourites->count() }}

                <form action="/replies/{{ $reply->id }}/favourites" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-default" {{ $reply->isFavourited() ? 'disabled' : '' }}>
                        {{ $reply->favourites_count }} {{ str_plural('Favourite', $reply->favourites_count) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        {{ $reply->body }}
    </div>
</div>