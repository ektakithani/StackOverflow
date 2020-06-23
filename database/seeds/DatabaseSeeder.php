<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        factory(\App\User::class, 5)->create()->each(function ($user){
           $user->questions()->saveMany(factory(\App\Question::class, rand(2,5))->make())->each(function($question){
               $question->answers()->saveMany(factory(\App\Answer::class, rand(2,7))->make());
           });
        });
    }
}
