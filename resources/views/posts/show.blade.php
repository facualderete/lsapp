@extends('layouts.app')

@section('content')
    <a href="/posts" class="btn btn-outline-secondary">Go back</a>
    <h1>{{ $post->title }}</h1>
    <div>
        {!! $post->body !!}
    </div>
    <hr>
    <small>Written on {{ $post->created_at }} - {{ $post->user->name }}</small>
    <hr>

    @auth        
        @if (Auth::user()->id == $post->user_id)            
            <a href="/posts/{{ $post->id }}/edit" class="btn btn-outline-secondary">Edit</a>

            {!! Form::open([
                'action' => ['PostsController@destroy', $post->id],
                'method' => 'DELETE',
                'class' => 'pull-right'
            ]) !!}

                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}

            {!! Form::close() !!}
        @endif
    @endauth
    
@endsection