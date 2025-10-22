@extends('bloglayout')

@section('title', "Kezdőlap")

@section('content')

@if (Session::has('post-created'))
    <div class="bg-green-300 rounded mb-2 py-2 text-center">
        A(z) {{ Session::get('post-created') }} című bejegyzés létrehozva!
    </div>
@elseif (Session::has('post-updated'))
    <div class="bg-green-300 rounded mb-2 py-2 text-center">
        A(z) {{ Session::get('post-updated') }} című bejegyzés módosítva!
    </div>
@elseif (Session::has('post-deleted'))
    <div class="bg-green-300 rounded mb-2 py-2 text-center">
        A(z) {{ Session::get('post-deleted') }} című bejegyzés törölve!
    </div>
@endif

@can('create', \App\Models\Post::class)
    <a href="{{ route('posts.create')}}" class="text-green-500">Új bejegyzés létrehozása</a>
    <br><br>
@endcan

<ul>
    @foreach($posts as $post)
        <li><a href="
            {{ route('posts.show', ['post' => $post ]) }}">{{$post -> title}}</a> <i>({{ $post -> author -> name }})</i></li>
    @endforeach
</ul>

{{ $posts -> links() }}
@endsection
