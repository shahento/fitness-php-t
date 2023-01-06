<?php

use Illuminate\Database\Seeder;
use App\Data\Entities\Amenity;

class AminetySeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $amenities = [[
            'name' => 'Restroom',
            'icon' => 'restroom.png',
        ],[
            'name' => 'Shower',
            'icon' => 'shower.png',
        ],[
            'name' => 'Locker Room',
            'icon' => 'locker.png',
        ],[
            'name' => 'Free towel service',
            'icon' => 'towel.png',
        ],[
            'name' => 'TV',
            'icon' => 'tv.png',
        ],[
            'name' => 'cafeteria',
            'icon' => 'cafeteria.png',
        ],[
            'name' => 'Juice Bar',
            'icon' => 'juice.png',
        ]];
        foreach($amenities as $row) {
            Amenity::create($row);
        }
        
    }

}
