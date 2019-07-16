<reply :attributes="{{ $reply }}" inline-template v-cloak>
    <div id="reply-{{ $reply->id }}" class="card">
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
            <div v-if="editing">
                <textarea class="form-control" v-model="body"></textarea>

                <button class="btn btn-xs btn-primary" @click="update">Update</button>
                <button class="btn btn-xs btn-link" @click="editing = false">Cancel</button>
            </div>
            <div v-else v-text="body"></div>
        </div>
        @can('manage', $reply)
            <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>

            <form action="/replies/{{ $reply->id }}" method="POST">
            @csrf
            @method('DELETE')
                <button type="submit" class="btn btn-default">Delete</button>
            </form>
        @endcan
    </div>
</reply>