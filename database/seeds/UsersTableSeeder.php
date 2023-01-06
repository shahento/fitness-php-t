<?php

use Illuminate\Database\Seeder;
use App\Data\Entities\User;

class UsersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        User::create([
            'first_name' => 'Administrator',
            'last_name' => '1',
            'email' => 'admin@confianzit.biz',
            'user_type' => 1,
            'email_verified' => true,
            'number_verified' => true,
            'contact_number' => '76546',
            'password' => bcrypt('Confianz123#')
        ]);
        // User::create([
        //     'first_name' => 'Vidya',
        //     'last_name' => 'Jayan',
        //     'email' => 'vidya.j.nair@confianzit.biz',
        //     'user_type' => 2,
        //     'email_verified' => true,
        //     'number_verified' => true,
        //     'contact_number' => '76546',
        //     'password' => bcrypt('Confianz123#')
        // ]);
        // User::create([
        //     'first_name' => 'Arjun',
        //     'last_name' => 'Jayan',
        //     'email' => 'arjun@gmail.cam',
        //     'user_type' => 2,
        //     'email_verified' => true,
        //     'number_verified' => true,
        //     'contact_number' => '76546',
        //     'password' => bcrypt('Confianz123#')
        // ]);
        // User::create([
        //     'first_name' => 'Shikha',
        //     'last_name' => 'Santhosh',
        //     'email' => 'shikha@gmail.cam',
        //     'user_type' => 3,
        //     'email_verified' => true,
        //     'number_verified' => true,
        //     'contact_number' => '76546',
        //     'password' => bcrypt('Confianz123#')
        // ]);
        // User::create([
        //     'first_name' => 'Anand',
        //     'last_name' => 'SG',
        //     'email' => 'anand@gmail.com',
        //     'user_type' => 3,
        //     'email_verified' => true,
        //     'number_verified' => true,
        //     'contact_number' => '76546',
        //     'password' => bcrypt('Confianz123#')
        // ]);
    }

}
