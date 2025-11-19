<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Post;
use App\Http\Resources\PostResource;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\PostStoreOrUpdateRequest;

class ApiController extends Controller
{
    public function login(Request $request){
        $validated = $request -> validate([
            "email" => "required|string|email",
            "password" => "required|string"
        ]);

        if (Auth::attempt($validated)){
            $user = User::where('email', $validated['email']) -> first();
            $token = $user -> createToken('loginToken');
            return response() -> json(["token" => $token -> plainTextToken ], 201);
        } else {
            return response() -> json(["message" => "Login failed."], 401);
        }
    }

    public function index(){
        $posts = Post::all();
        //return response() -> json($posts);
        return PostResource::collection($posts);
    }

    public function show(string $post){
        // if (filter_var($post, FILTER_VALIDATE_INT) === false){
        //     return response() -> json(["message" => 'post must be an integer!'], 422);
        // }

        validator(
            ['post' => $post],
            ['post' => 'required|integer']
        ) -> validate();

        // $post = Post::find($post);
        // if ($post)
        //     return new PostResource($post);
        // else
        //     return response() -> json(["message" => 'Post not found!'], 404);

        $post = Post::findOrFail($post);
        return new PostResource($post);
    }

    public function store(PostStoreOrUpdateRequest $request){
        $validated = $request -> validated();
        $validated['is_public'] = $request -> has('is_public');
        $validated['author_id'] = $request -> user() -> id;
        $post = Post::create($validated);
        return new PostResource($post);
    }

    public function update(Request $request, string $post){
        validator(
            ['post' => $post],
            ['post' => 'required|integer']
        ) -> validate();
        $post = Post::findOrFail($post);
        if (!$request -> user() -> is_admin && $request -> user() -> id !== $post ->author_id)
            return response() -> json(["message" => "You cannot do this."], 403);
        $validated = $request -> validate([
            "title" => "string|min:10",
            "content" => "string|max:999",
            "is_public" => "boolean",
            'author_id' => "integer|exists:users,id"
        ]);
        $post -> update($validated);
        return new PostResource($post);
    }

    public function indexCategories(string $post){
        validator(
            ['post' => $post],
            ['post' => 'required|integer']
        ) -> validate();
        $post = Post::findOrFail($post);
        return CategoryResource::collection($post -> categories);
    }

    public function indexWithCategories(){
        $posts = Post::with('categories') -> get();
        return PostResource::collection($posts);
    }
}
