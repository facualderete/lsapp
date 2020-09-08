@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    {{-- @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif --}}
                    <a href="/posts/create" class="btn btn-primary">Create Post</a>
                    <h3>Your blog posts</h3>
                    @if (count($posts) > 0)                        
                        <table class="table table-striped">
                            <tr>
                                <th>Title</th>
                                <th></th>
                                <th></th>
                            </tr>
                            @foreach ($posts as $post)
                                <tr>
                                    <td>{{ $post->title }}</td>
                                    <td><a class="btn btn-primary" href="/posts/{{ $post->id }}/edit">Edit</a></td>
                                    <td>
                                        {!! Form::open([
                                            'action' => ['PostsController@destroy', $post->id],
                                            'method' => 'DELETE',
                                            'class' => 'pull-right'
                                        ]) !!}
                                    
                                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                    
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <div class="alert alert-danger">
                            No posts yet!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection