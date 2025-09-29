<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();
        $posts = collect();
        for ($i = 0; $i < 20; $i++){
            $posts -> add(Post::create([
                'title' => fake() -> words(3, true),
                'content' => fake() -> paragraph(),
                'is_public' => fake() -> boolean(),
                'author_id' => $users -> random() -> id // 1:N
            ]));
        }
        for ($i = 0; $i < 5; $i++){
            $c = Category::create([
                'name' => fake() -> word(),
                'color' => fake() -> hexColor()
            ]);
            $c -> posts() -> sync( $posts -> random(rand(1, 5)) -> pluck('id') ); // N:N
        }
    }
}
