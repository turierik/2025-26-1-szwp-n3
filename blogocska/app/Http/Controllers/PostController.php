<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\PostStoreOrUpdateRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class PostController extends Controller
{
    public function index(){
        //$kiskutya = Post::all();
        $kiskutya = Post::with('author') -> paginate(10);
        return view('posts.index', ['posts' => $kiskutya]);
    }

    public function show(Post $post){
        return view('posts.show', ['post' => $post ]);
    }

    public function create(){
        Gate::authorize('create', Post::class);
        return view('posts.create', [
            'users' => User::all(),
            'categories' => Category::all()
        ]);
    }

    public function store(PostStoreOrUpdateRequest $request){
        Gate::authorize('create', Post::class);
        $validated = $request -> validated();
        $validated['is_public'] = $request -> has('is_public');
        $validated['author_id'] = Auth::user() -> id;  // Auth::id()

        if ($request -> hasFile('image_file')){
            $file = $request -> file('image_file');
            $fileName = Str::uuid() . "." . $file -> getClientOriginalExtension();
            Storage::disk('public') -> put("images/".$fileName, $file -> getContent());
            $validated['image'] = $fileName;
        }

        $post = Post::create($validated);
        $post -> categories() -> sync($validated['categories'] ?? []);
        Session::flash('post-created', $post -> title);
        return redirect() -> route('posts.index');
    }

    public function edit(Post $post){
        Gate::authorize('update', $post);
        return view('posts.edit', [
            'users' => User::all(),
            'categories' => Category::all(),
            'post' => $post
        ]);
    }

    public function update(PostStoreOrUpdateRequest $request, Post $post){
        Gate::authorize('update', $post);
        $validated = $request -> validated();
        $validated['is_public'] = $request -> has('is_public');
        $post -> update($validated);
        $post -> categories() -> sync($validated['categories'] ?? []);
        Session::flash('post-updated', $post -> title);
        return redirect() -> route('posts.index');
    }

    public function destroy(Post $post){
        Gate::authorize('delete', $post);
        $title = $post -> title;
        $post -> delete(); // Post::destroy($post -> id);
        Session::flash('post-deleted', $title);
        return redirect() -> route('posts.index');
    }
}
