<?php

use Illuminate\Database\Seeder;
use App\Data\Entities\State;
use App\Data\Entities\Country;
use Illuminate\Support\Arr;
class StateTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        
        $id  = Country::where('name', 'United States')->first()->id;
        $states = [
            ['country_id' => $id, 'name' => 'Alabama'],
            ['country_id' => $id, 'name' => 'Alaska'],
            ['country_id' => $id, 'name' => 'Arizona'],
            ['country_id' => $id, 'name' => 'Arkansas'],
            ['country_id' => $id, 'name' => 'California'],
            ['country_id' => $id, 'name' => 'Colorado'],
            ['country_id' => $id, 'name' => 'Connecticut'],
            ['country_id' => $id, 'name' => 'Delaware'],
            ['country_id' => $id, 'name' => 'District of Columbia'],
            ['country_id' => $id, 'name' => 'Florida'],
            ['country_id' => $id, 'name' => 'Georgia'],
            ['country_id' => $id, 'name' => 'Hawaii'],
            ['country_id' => $id, 'name' => 'Idaho'],
            ['country_id' => $id, 'name' => 'Illinois'],
            ['country_id' => $id, 'name' => 'Indiana'],
            ['country_id' => $id, 'name' => 'Iowa'],
            ['country_id' => $id, 'name' => 'Kansas'],
            ['country_id' => $id, 'name' => 'Kentucky'],
            ['country_id' => $id, 'name' => 'Louisiana'],
            ['country_id' => $id, 'name' => 'Maine'],
            ['country_id' => $id, 'name' => 'Maryland'],
            ['country_id' => $id, 'name' => 'Massachusetts'],
            ['country_id' => $id, 'name' => 'Michigan'],
            ['country_id' => $id, 'name' => 'Minnesota'],
            ['country_id' => $id, 'name' => 'Mississippi'],
            ['country_id' => $id, 'name' => 'Missouri'],
            ['country_id' => $id, 'name' => 'Montana'],
            ['country_id' => $id, 'name' => 'Nebraska'],
            ['country_id' => $id, 'name' => 'Nevada'],
            ['country_id' => $id, 'name' => 'New Hampshire'],
            ['country_id' => $id, 'name' => 'New Jersey'],
            ['country_id' => $id, 'name' => 'New Mexico'],
            ['country_id' => $id, 'name' => 'New York'],
            ['country_id' => $id, 'name' => 'North Carolina'],
            ['country_id' => $id, 'name' => 'North Dakota'],
            ['country_id' => $id, 'name' => 'Ohio'],
            ['country_id' => $id, 'name' => 'Oklahoma'],
            ['country_id' => $id, 'name' => 'Oregon'],
            ['country_id' => $id, 'name' => 'Pennsylvania'],
            ['country_id' => $id, 'name' => 'Puerto Rico'],
            ['country_id' => $id, 'name' => 'Rhode Island'],
            ['country_id' => $id, 'name' => 'South Carolina'],
            ['country_id' => $id, 'name' => 'South Dakota'],
            ['country_id' => $id, 'name' => 'Tennessee'],
            ['country_id' => $id, 'name' => 'Texas'],
            ['country_id' => $id, 'name' => 'Utah'],
            ['country_id' => $id, 'name' => 'Vermont'],
            ['country_id' => $id, 'name' => 'Virginia'],
            ['country_id' => $id, 'name' => 'Washington'],
            ['country_id' => $id, 'name' => 'West Virginia'],
            ['country_id' => $id, 'name' => 'Wisconsin'],
            ['country_id' => $id, 'name' => 'Wyoming']
        ];
       
        $states = array_values(Arr::sort($states, function ($value) {
                    return $value['name'];
                }));

        State::insert($states);
    }

}
