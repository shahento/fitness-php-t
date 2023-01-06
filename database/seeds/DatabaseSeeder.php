<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UsersTableSeeder::class);
         $this->call(CountryTableSeeder::class);
         $this->call(StateTableSeeder::class);
         $this->call(AminetySeeder::class);
    }
}
