<?php

use Illuminate\Database\Seeder;
use App\Data\Entities\Amenity;

class UpdateAminetySeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
            Amenity::where('name','cafeteria')->update(
                [
                    'name'=>'Concessions'
                ]
            );
                
    }

}
