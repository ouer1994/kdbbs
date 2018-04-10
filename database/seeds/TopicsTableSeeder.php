<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Topic;

class TopicsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = app(Faker\Generator::class);
        $users = User::all()->pluck('id')->toArray();
        $categories = Category::all()->pluck('id')->toArray();

        $topics = factory(Topic::class)->times(100)->make()->each(function ($topic, $index) use ($faker, $users, $categories) {
            $topic->user_id = $faker->randomElement($users);
            $topic->category_id = $faker->randomElement($categories);
        });

        Topic::insert($topics->toArray());
    }

}

