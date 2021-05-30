<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 3; $i++) {
            DB::table('events')->insert([
                'title' => Str::random(5),
                'content' => Str::random(70),
                'finish_at' => '2002-02-02 22:22:22',
                'type' => 'arrangement',
                'color' => 'red',
                'user_id' => 1,
                'created_at' => '2000-01-01 00:00:00',
                'updated_at' => '2000-01-01 00:00:00',
            ]);

            DB::table('events')->insert([
                'title' => Str::random(5),
                'content' => Str::random(70),
                'finish_at' => '2002-02-02 22:22:22',
                'type' => 'reminder',
                'color' => 'blue',
                'user_id' => 2,
                'created_at' =>'2000-01-01 00:00:00',
                'updated_at' => '2000-01-01 00:00:00',
            ]);

            DB::table('events')->insert([
                'title' => Str::random(5),
                'content' => Str::random(70),
                'finish_at' => '2002-02-02 22:22:22',
                'type' => 'task',
                'color' => 'yellow',
                'user_id' => 3,
                'created_at' => '2000-01-01 00:00:00',
                'updated_at' => '2000-01-01 00:00:00',
            ]);
        }
    }
}
