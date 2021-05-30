<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

//php artisan migrate:refresh --seed
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            EventSeeder::class,
            GroupSeeder::class,
        ]);
    }
}
